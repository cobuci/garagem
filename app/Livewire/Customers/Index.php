<?php

namespace App\Livewire\Customers;

use App\Enums\Permission;
use App\Enums\SaleStatus;
use App\Models\Customer;
use App\Models\Sale;
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
 * @property-read array{total_customers: int, customers_in_debt: int, total_due: float} $stats
 */
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    protected $queryString = [
        'search'        => ['except' => ''],
        'sortField'     => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    /** @var array<string, string> */
    protected array $allowedSortFields = [
        'name'      => 'name',
        'total_due' => 'sales_sum_total_amount',
    ];

    public function mount(): void
    {
        $this->authorize(Permission::ViewCustomer->value);
    }

    public function sort(string $field): void
    {
        if (! array_key_exists($field, $this->allowedSortFields)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            $this->resetPage();

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = $field === 'total_due' ? 'desc' : 'asc';
        $this->resetPage();
    }

    /**
     * @return array{total_customers: int, customers_in_debt: int, total_due: float}
     */
    #[Computed, On(['customer:created', 'customer:updated', 'sale:created', 'sale:updated'])]
    public function stats(): array
    {
        $totalCustomers = Customer::count();
        $totalDue = (float) (Sale::where('status', SaleStatus::Pending)->whereNotNull('customer_id')->sum('total_amount') / 100);
        $customersInDebt = Customer::whereHas('sales', fn (Builder $q) => $q->where('status', SaleStatus::Pending))->count();

        return [
            'total_customers'   => $totalCustomers,
            'customers_in_debt' => $customersInDebt,
            'total_due'         => $totalDue,
        ];
    }

    #[Computed, On(['customer:created', 'customer:updated', 'sale:created', 'sale:updated'])]
    public function customers(): LengthAwarePaginator
    {
        $column = $this->allowedSortFields[$this->sortField] ?? 'name';

        return Customer::query()
            ->withSum(['sales' => fn (Builder $query) => $query->where('status', SaleStatus::Pending)], 'total_amount')
            ->filters(['search' => $this->search])
            ->orderBy($column, $this->sortDirection)
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
