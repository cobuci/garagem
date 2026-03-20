<?php

namespace App\Livewire\Settings;

use App\Livewire\Forms\SettingsForm;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\View\View;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Index extends Component
{
    use WireUiActions;

    public User $user;

    public SettingsForm $form;

    public function mount(#[CurrentUser] User $user): void
    {
        $this->user = $user;
        $this->form->setSettings(Setting::singleton(), $this->user);
    }

    public function save(): void
    {
        $localeChanged = $this->form->update($this->user);

        $this->notification()->success(
            title: __('settings.actions.success'),
        );

        if ($localeChanged) {
            session()->put('locale', $this->form->locale);
            app()->setLocale($this->form->locale);
            $this->redirect(route('settings.index'), navigate: true);
        }
    }

    public function render(): View
    {
        return view('livewire.settings.index')
            ->layout('layouts.app');
    }
}
