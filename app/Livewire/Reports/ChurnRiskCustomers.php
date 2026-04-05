<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Enums\SaleStatus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ChurnRiskCustomers extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $sortField = 'days_since_last';

    public string $sortDirection = 'desc';

    /** @var array<string, string> */
    protected array $allowedSortFields = [
        'name'            => 'customers.name',
        'total_purchases' => 'total_purchases',
        'avg_interval'    => 'avg_interval_days',
        'days_since_last' => 'days_since_last',
        'urgency'         => 'days_since_last',
        'total_spent'     => 'total_spent',
    ];

    public function mount(): void
    {
        $this->authorize(Permission::ViewReport->value);
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
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function render(): View
    {
        $customers = $this->getAtRiskCustomers();

        return view('livewire.reports.churn-risk-customers', [
            'customers'   => $customers,
            'atRiskCount' => $customers->total(),
        ]);
    }

    public function placeholder(): View
    {
        return view('livewire.reports.placeholders.churn-risk-customers');
    }

    private function getAtRiskCustomers(): LengthAwarePaginator
    {
        $cancelled = SaleStatus::Cancelled->value;
        $orderCol = $this->allowedSortFields[$this->sortField] ?? 'days_since_last';
        $orderDir = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return DB::table('customers')
            ->join('sales', 'sales.customer_id', '=', 'customers.id')
            ->select([
                'customers.id',
                'customers.name',
                DB::raw('COUNT(sales.id) as total_purchases'),
                DB::raw('ROUND(DATEDIFF(MAX(sales.created_at), MIN(sales.created_at)) / NULLIF(COUNT(sales.id) - 1, 0), 1) as avg_interval_days'),
                DB::raw('DATEDIFF(NOW(), MAX(sales.created_at)) as days_since_last'),
                DB::raw('MAX(sales.created_at) as last_purchase'),
                DB::raw('SUM(sales.total_amount) as total_spent'),
            ])
            ->where('sales.status', '!=', $cancelled)
            ->whereNull('customers.deleted_at')
            ->groupBy('customers.id', 'customers.name')
            ->havingRaw('COUNT(sales.id) >= 3')
            ->havingRaw('DATEDIFF(NOW(), MAX(sales.created_at)) <= 365')
            ->havingRaw('DATEDIFF(MAX(sales.created_at), MIN(sales.created_at)) / NULLIF(COUNT(sales.id) - 1, 0) <= 180')
            ->havingRaw(
                'DATEDIFF(NOW(), MAX(sales.created_at)) > 2 * (DATEDIFF(MAX(sales.created_at), MIN(sales.created_at)) / NULLIF(COUNT(sales.id) - 1, 0))',
            )
            ->orderBy($orderCol, $orderDir)
            ->paginate(10);
    }

    public static function urgencyLevel(float $daysSince, float $avgInterval): string
    {
        if ($avgInterval <= 0) {
            return 'medium';
        }

        $ratio = $daysSince / $avgInterval;

        return match (true) {
            $ratio > 4 => 'critical',
            $ratio > 3 => 'high',
            default    => 'medium',
        };
    }
}
