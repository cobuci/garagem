<?php

namespace App\Livewire\Banners;

use App\Enums\BannerIntensity;
use App\Enums\BannerJobStatus;
use App\Enums\BannerMood;
use App\Enums\BannerTheme;
use App\Enums\Permission;
use App\Jobs\ExportBannerJob;
use App\Jobs\GenerateBannerBackgroundJob;
use App\Models\Banner;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WireUi\Traits\WireUiActions;

class Studio extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public Banner $banner;

    public array $design = [];

    public ?string $backgroundTheme = null;

    public ?string $backgroundMood = null;

    public ?string $backgroundIntensity = null;

    public int $exportScale = 2;

    public function mount(Banner $banner): void
    {
        $this->authorize(Permission::ViewBanner->value);

        $this->banner = $banner;
        $this->design = array_merge(Banner::defaultDesign(), $banner->design);
        $this->backgroundTheme = $banner->background_theme?->value;
        $this->backgroundMood = $banner->background_mood?->value;
        $this->backgroundIntensity = $banner->background_intensity?->value;
    }

    protected function rules(): array
    {
        return [
            'design.title'            => ['required', 'string', 'max:80'],
            'design.subtitle'         => ['nullable', 'string', 'max:120'],
            'design.footer'           => ['nullable', 'string', 'max:120'],
            'design.background_color' => ['required', 'string', 'max:9'],
            'design.accent_color'     => ['required', 'string', 'max:9'],
            'design.text_color'       => ['required', 'string', 'max:9'],
            'design.show_logo'        => ['boolean'],
            'design.logo_position'    => ['required', Rule::in(['top', 'bottom'])],
            'design.logo_size'        => ['required', Rule::in(['small', 'medium', 'large'])],
            'design.items'            => ['array', 'max:14'],
            'design.items.*.name'     => ['nullable', 'string', 'max:60'],
            'design.items.*.note'     => ['nullable', 'string', 'max:30'],
            'design.items.*.price'    => ['nullable', 'string', 'max:20'],
        ];
    }

    public function save(): void
    {
        $this->authorize(Permission::EditBanner->value);

        $this->validate();
        $this->banner->update(['design' => $this->design]);

        $this->notification()->success(
            title: __('banners.messages.success'),
            description: __('banners.messages.saved'),
        );
    }

    public function applyPreset(string $preset): void
    {
        $this->authorize(Permission::EditBanner->value);

        $presets = Banner::presets();

        abort_unless(array_key_exists($preset, $presets), 404);

        $this->design = array_merge($this->design, $presets[$preset]);
    }

    public function addItem(): void
    {
        $this->design['items'][] = ['name' => '', 'note' => '', 'price' => ''];
    }

    public function removeItem(int $index): void
    {
        unset($this->design['items'][$index]);
        $this->design['items'] = array_values($this->design['items']);
    }

    public function selectTheme(string $theme): void
    {
        $this->backgroundTheme = $theme;
    }

    public function generateBackground(): void
    {
        $this->authorize(Permission::EditBanner->value);

        $this->validate([
            'backgroundTheme'     => ['required', Rule::enum(BannerTheme::class)],
            'backgroundMood'      => ['nullable', Rule::enum(BannerMood::class)],
            'backgroundIntensity' => ['nullable', Rule::enum(BannerIntensity::class)],
        ]);

        $this->banner->update([
            'design'               => $this->design,
            'background_theme'     => $this->backgroundTheme,
            'background_mood'      => $this->backgroundMood,
            'background_intensity' => $this->backgroundIntensity,
            'background_status'    => BannerJobStatus::Generating,
        ]);

        GenerateBannerBackgroundJob::dispatch($this->banner);
    }

    public function removeBackground(): void
    {
        $this->authorize(Permission::EditBanner->value);

        if ($this->banner->background_path) {
            Storage::delete($this->banner->background_path);
        }

        $this->banner->update([
            'background_path'   => null,
            'background_status' => BannerJobStatus::None,
        ]);
    }

    public function export(): void
    {
        $this->authorize(Permission::EditBanner->value);

        $this->validate(['exportScale' => ['required', 'integer', 'between:1,3']]);

        $this->banner->update([
            'design'        => $this->design,
            'export_status' => BannerJobStatus::Generating,
        ]);

        ExportBannerJob::dispatch($this->banner, $this->exportScale);
    }

    public function refreshStatus(): void
    {
        $this->banner->refresh();
    }

    public function downloadPng(): StreamedResponse
    {
        return Storage::download(
            $this->banner->export_png_path,
            Str::slug($this->banner->name) . '.png',
        );
    }

    public function downloadPdf(): StreamedResponse
    {
        return Storage::download(
            $this->banner->export_pdf_path,
            Str::slug($this->banner->name) . '.pdf',
        );
    }

    public function render(): View
    {
        return view('livewire.banners.studio');
    }
}
