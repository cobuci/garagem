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

class StockTurnover extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $sortField = 'days_remaining';

    public string $sortDirection = 'asc';

    /** @var array<string, string> */
    protected array $allowedSortFields = [
        'product'        => 'p.name',
        'category'       => 'c.name',
        'stock'          => 'p.stock_quantity',
        'units_per_day'  => 'units_per_day',
        'days_remaining' => 'days_remaining',
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
        $items = $this->getData();

        return view('livewire.reports.stock-turnover', [
            'items'      => $items,
            'alertCount' => $this->getAlertCount(),
        ]);
    }

    public function placeholder(): View
    {
        return view('livewire.reports.placeholders.stock-turnover');
    }

    private function getData(): LengthAwarePaginator
    {
        $orderCol = $this->allowedSortFields[$this->sortField] ?? 'days_remaining';
        $orderDir = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return DB::table('products as p')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->select([
                'p.id',
                'p.name',
                'p.brand',
                'p.weight',
                'c.name as category_name',
                'p.stock_quantity',
                DB::raw('ROUND((
                    SELECT SUM(si.quantity) 
                    FROM sale_items si 
                    JOIN sales s ON s.id = si.sale_id 
                    WHERE si.product_id = p.id 
                      AND s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                      AND s.status != "cancelled"
                ) / 30, 2) as units_per_day'),
                DB::raw('ROUND(p.stock_quantity / NULLIF((
                    SELECT SUM(si.quantity) 
                    FROM sale_items si 
                    JOIN sales s ON s.id = si.sale_id 
                    WHERE si.product_id = p.id 
                      AND s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                      AND s.status != "cancelled"
                ) / 30, 0), 1) as days_remaining'),
            ])
            ->whereNull('p.deleted_at')
            ->where('p.stock_quantity', '>', 0)
            ->havingRaw('units_per_day > 0')
            ->orderBy($orderCol, $orderDir)
            ->paginate(15);
    }

    private function getAlertCount(): int
    {
        return DB::table('products as p')
            ->select('p.id')
            ->whereNull('p.deleted_at')
            ->where('p.stock_quantity', '>', 0)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sale_items as si')
                    ->join('sales as s', 's.id', '=', 'si.sale_id')
                    ->whereRaw('si.product_id = p.id')
                    ->where('s.created_at', '>=', now()->subDays(30))
                    ->where('s.status', '!=', SaleStatus::Cancelled->value);
            })
            ->whereRaw('p.stock_quantity / NULLIF((
                SELECT SUM(si.quantity) 
                FROM sale_items si 
                JOIN sales s ON s.id = si.sale_id 
                WHERE si.product_id = p.id 
                  AND s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                  AND s.status != "cancelled"
            ) / 30, 0) < 7')
            ->count();
    }

    public static function stockStatus(float $daysRemaining): string
    {
        return match (true) {
            $daysRemaining < 3 => 'critical',
            $daysRemaining < 7 => 'low',
            default            => 'ok',
        };
    }
}
