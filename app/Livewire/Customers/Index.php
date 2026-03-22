<?php

namespace App\Livewire\Customers;

use App\Enums\Permission;
use App\Enums\SaleStatus;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    protected $queryString = ['search' => ['except' => '']];

    public function mount(): void
    {
        $this->authorize(Permission::ViewCustomer->value);
    }

    #[Computed, On(['customer:created', 'customer:updated', 'sale:created', 'sale:updated'])]
    public function customers(): LengthAwarePaginator
    {
        return Customer::query()
            ->withSum(['sales' => fn (Builder $query) => $query->where('status', SaleStatus::Pending)], 'total_amount')
            ->filters(['search' => $this->search])
            ->orderBy('name')
            ->paginate(10);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.customers.index');
    }
}
