<?php

namespace App\Livewire\Banners;

use App\Enums\BannerJobStatus;
use App\Enums\Permission;
use App\Jobs\ExportBannerJob;
use App\Jobs\GenerateBannerBackgroundJob;
use App\Models\Banner;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public string $backgroundPrompt = '';

    public int $exportScale = 2;

    public function mount(Banner $banner): void
    {
        $this->authorize(Permission::ViewBanner->value);

        $this->banner = $banner;
        $this->design = $banner->design;
        $this->backgroundPrompt = (string) $banner->background_prompt;
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

    public function addItem(): void
    {
        $this->design['items'][] = ['name' => '', 'note' => '', 'price' => ''];
    }

    public function removeItem(int $index): void
    {
        unset($this->design['items'][$index]);
        $this->design['items'] = array_values($this->design['items']);
    }

    public function generateBackground(): void
    {
        $this->authorize(Permission::EditBanner->value);

        $this->validate(['backgroundPrompt' => ['required', 'string', 'max:500']]);

        $this->banner->update([
            'design'            => $this->design,
            'background_prompt' => $this->backgroundPrompt,
            'background_status' => BannerJobStatus::Generating,
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
