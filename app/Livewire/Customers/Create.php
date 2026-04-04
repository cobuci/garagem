<?php

namespace App\Livewire\Customers;

use App\Enums\Permission;
use App\Livewire\Forms\CustomerForm;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Create extends Component
{
    use WireUiActions;

    public CustomerForm $form;

    public bool $showDrawer = false;

    #[On('open-drawer')]
    public function open(string $component): void
    {
        if ($component === 'customers.create') {
            $this->showDrawer = true;
        }
    }

    public function save(): void
    {
        $this->authorize(Permission::CreateCustomer->value);

        $this->form->store();

        $this->notification()->success(__('customers.created'));

        $this->dispatch('customer:created');

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
        return view('livewire.customers.create');
    }
}
