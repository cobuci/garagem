<?php

namespace App\Livewire\Settings;

use App\Enums\Permission;
use App\Livewire\Forms\SettingsForm;
use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Index extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public User $user;

    public SettingsForm $form;

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }

    public function mount(#[CurrentUser] User $user): void
    {
        $this->user = $user;
        $this->form->setSettings(Setting::singleton(), $this->user);
    }

    public function updatedFormLocale(string $value): void
    {
        $this->user->update(['locale' => $value]);
        session()->put('locale', $value);
        app()->setLocale($value);

        $this->redirect(route('settings.index'), navigate: true);
    }

    public function save(): void
    {
        $this->authorize(Permission::EditSetting->value);

        $this->form->update($this->user);

        config(['app.name' => Setting::singleton()->store_name]);

        $this->notification()->success(
            title: __('settings.actions.success'),
        );
    }

    public function render(): View
    {
        return view('livewire.settings.index')
            ->layout('layouts.app');
    }
}
