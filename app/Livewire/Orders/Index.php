<?php

namespace App\Livewire\Orders;

use App\Enums\Permission;
use Livewire\Component;

class Index extends Component
{
    public function mount(): void
    {
        $this->authorize(Permission::ViewSale->value);
    }

    public function render()
    {
        return view('livewire.orders.index');
    }
}
