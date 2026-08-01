<?php

namespace App\Livewire\Banners;

use App\Enums\BannerFormat;
use App\Enums\Permission;
use App\Models\Banner;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

/**
 * @property-read Collection<int, Banner> $banners
 */
class Index extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public bool $showDrawer = false;

    public string $name = '';

    public string $format = BannerFormat::Stories->value;

    public function mount(): void
    {
        $this->authorize(Permission::ViewBanner->value);
    }

    #[Computed]
    public function banners(): Collection
    {
        return Banner::query()
            ->with('user:id,name')
            ->latest()
            ->get();
    }

    public function create(): void
    {
        $this->authorize(Permission::CreateBanner->value);

        $this->reset('name', 'format');
        $this->resetErrorBag();
        $this->showDrawer = true;
    }

    public function store(): void
    {
        $this->authorize(Permission::CreateBanner->value);

        $validated = $this->validate([
            'name'   => ['required', 'string', 'max:100'],
            'format' => ['required', Rule::enum(BannerFormat::class)],
        ]);

        $banner = Banner::create([
            'user_id' => Auth::id(),
            'name'    => $validated['name'],
            'format'  => $validated['format'],
            'design'  => Banner::defaultDesign(),
        ]);

        $this->redirectRoute('banners.studio', $banner, navigate: true);
    }

    public function delete(int $bannerId): void
    {
        $this->authorize(Permission::DeleteBanner->value);

        $banner = Banner::query()->findOrFail($bannerId);

        Storage::deleteDirectory("banners/{$banner->id}");
        $banner->delete();

        $this->notification()->success(
            title: __('banners.messages.success'),
            description: __('banners.messages.deleted'),
        );

        unset($this->banners);
    }

    public function render(): View
    {
        return view('livewire.banners.index');
    }
}
