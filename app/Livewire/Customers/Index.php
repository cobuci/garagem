<?php

namespace App\Livewire\Customers;

use App\Enums\SaleStatus;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read LengthAwarePaginator $customers
 */
class Index extends Component
{
    use WithPagination;

    #[Computed, On(['customer:created', 'customer:updated', 'sale:created', 'sale:updated'])]
    public function customers(): LengthAwarePaginator
    {
        return Customer::query()
            ->withSum(['sales' => fn (Builder $query) => $query->where('status', SaleStatus::Pending)], 'total_amount')
            ->latest()
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.customers.index');
    }
}
