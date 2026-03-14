<?php

namespace App\Livewire\Forms;

use App\Enums\Gender;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CustomerForm extends Form
{
    public string $name = '';

    public ?string $email = null;

    public ?string $phone = null;

    public ?string $gender = null;

    public ?string $zip_code = null;

    public ?string $address = null;

    public ?string $street = null;

    public ?string $neighborhood = null;

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255', 'unique:customers,name'],
            'email'        => ['nullable', 'email', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'gender'       => ['nullable', Rule::enum(Gender::class)],
            'zip_code'     => ['nullable', 'string', 'max:10'],
            'address'      => ['nullable', 'string', 'max:255'],
            'street'       => ['nullable', 'string', 'max:255'],
            'neighborhood' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function store(): void
    {
        $this->validate();

        Customer::create([
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'gender'       => $this->gender,
            'zip_code'     => $this->zip_code,
            'address'      => $this->address,
            'street'       => $this->street,
            'neighborhood' => $this->neighborhood,
        ]);

        $this->reset();
    }
}
