<?php

namespace App\Livewire\Admin;

use App\Enums\Permission;
use App\Models\Banner;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;

class BrandLogo extends Component
{
    use AuthorizesRequests;
    use WireUiActions;
    use WithFileUploads;

    public $logo;

    public function mount(): void
    {
        $this->authorize(Permission::ViewAdmin->value);
    }

    public function save(): void
    {
        $this->authorize(Permission::EditSetting->value);

        $this->validate([
            'logo' => ['required', 'image', 'mimes:png', 'max:2048'],
        ]);

        Storage::disk('public')->makeDirectory('brand');
        $this->logo->storeAs('brand', basename(Banner::LOGO_STORAGE_PATH), 'public');
        $this->reset('logo');

        $this->notification()->success(
            title: __('admin.brand.messages.success'),
            description: __('admin.brand.messages.uploaded'),
        );
    }

    public function remove(): void
    {
        $this->authorize(Permission::EditSetting->value);

        Storage::disk('public')->delete(Banner::LOGO_STORAGE_PATH);
        $this->reset('logo');

        $this->notification()->success(
            title: __('admin.brand.messages.success'),
            description: __('admin.brand.messages.removed'),
        );
    }

    public function render(): View
    {
        return view('livewire.admin.brand-logo', [
            'hasLogo' => Banner::hasLogo(),
            'logoUrl' => Banner::logoUrl(),
        ]);
    }
}
