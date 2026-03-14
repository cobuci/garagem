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

    public ?Customer $customer = null;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('customers', 'name')->ignore($this->customer?->id),
            ],
            'email'        => ['nullable', 'email', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'gender'       => ['nullable', Rule::enum(Gender::class)],
            'zip_code'     => ['nullable', 'string', 'max:10'],
            'address'      => ['nullable', 'string', 'max:255'],
            'street'       => ['nullable', 'string', 'max:255'],
            'neighborhood' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;

        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->gender = $customer->gender?->value;
        $this->zip_code = $customer->zip_code;
        $this->address = $customer->address;
        $this->street = $customer->street;
        $this->neighborhood = $customer->neighborhood;
    }

    public function store(): void
    {
        $this->validate();

        Customer::create($this->except('customer'));

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $this->customer->update($this->except('customer'));
    }
}
