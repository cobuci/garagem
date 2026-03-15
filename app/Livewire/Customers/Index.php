<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Computed, On(['customer:created'])]
    public function customers(): LengthAwarePaginator
    {
        return Customer::query()
            ->latest()
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.customers.index');
    }
}
