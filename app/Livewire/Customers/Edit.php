<?php

namespace App\Livewire\Customers;

use App\Enums\Permission;
use App\Livewire\Forms\CustomerForm;
use App\Models\Customer;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Edit extends Component
{
    use WireUiActions;

    public CustomerForm $form;

    public bool $showDrawer = false;

    #[On('edit:customer')]
    public function open(int $customer): void
    {
        $customerModel = Customer::findOrFail($customer);
        $this->form->setCustomer($customerModel);
        $this->showDrawer = true;
    }

    public function save(): void
    {
        $this->authorize(Permission::EditCustomer->value);

        $this->form->update();

        $this->notification()->success(__('customers.updated'));

        $this->dispatch('customer:updated');

        $this->showDrawer = false;
    }

    public function updatedShowDrawer(bool $value): void
    {
        if (! $value) {
            $this->form->reset();
            $this->resetErrorBag();
        }
    }

    public function render(): View
    {
        return view('livewire.customers.edit');
    }
}
