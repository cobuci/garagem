<?php

namespace App\Livewire\Forms;

use App\Models\Setting;
use App\Models\User;
use Livewire\Form;

class SettingsForm extends Form
{
    public string $store_name = '';

    public $credit_card_fee = 0;

    public $debit_card_fee = 0;

    public string $address = '';

    public string $city = '';

    public string $state = '';

    public string $zip_code = '';

    public string $locale = 'pt_BR';

    public function rules(): array
    {
        return [
            'store_name'      => ['nullable', 'string', 'max:255'],
            'credit_card_fee' => ['required', 'numeric', 'min:0', 'max:100'],
            'debit_card_fee'  => ['required', 'numeric', 'min:0', 'max:100'],
            'address'         => ['nullable', 'string', 'max:255'],
            'city'            => ['nullable', 'string', 'max:255'],
            'state'           => ['nullable', 'string', 'max:255'],
            'zip_code'        => ['nullable', 'string', 'max:20'],
            'locale'          => ['required', 'string'],
        ];
    }

    public function setSettings(Setting $settings, User $user): void
    {
        $this->store_name = $settings->store_name ?? '';
        $this->credit_card_fee = (float) $settings->credit_card_fee;
        $this->debit_card_fee = (float) $settings->debit_card_fee;
        $this->address = $settings->address ?? '';
        $this->city = $settings->city ?? '';
        $this->state = $settings->state ?? '';
        $this->zip_code = $settings->zip_code ?? '';
        $this->locale = $user->locale ?? 'pt_BR';
    }

    public function update(User $user): bool
    {
        $this->validate();

        Setting::singleton()->update($this->except(['locale']));

        $localeChanged = $this->locale !== $user->locale;

        $user->update(['locale' => $this->locale]);

        return $localeChanged;
    }
}
