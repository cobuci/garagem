<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Enums\SaleStatus;
use App\Models\SaleItem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class MostProfitableProducts extends Component
{
    use AuthorizesRequests;

    public string $period = 'last_30_days';

    public array $chartDataArray = [];

    public function mount(): void
    {
        $this->authorize(Permission::ViewReport->value);
        $this->updateChartData();
    }

    public function updatedPeriod(): void
    {
        $this->updateChartData();
    }

    public function updateChartData(): void
    {
        [$startDate, $endDate] = $this->getPeriodConfig();

        $products = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->selectRaw('
                products.name as product_name,
                SUM((sale_items.unit_price - sale_items.unit_cost) * sale_items.quantity) as total_profit,
                SUM(sale_items.quantity) as total_quantity,
                AVG(sale_items.unit_price - sale_items.unit_cost) as avg_margin_per_unit
            ')
            ->where('sales.created_at', '>=', $startDate)
            ->where('sales.created_at', '<=', $endDate)
            ->where('sales.status', '!=', SaleStatus::Cancelled)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_profit')
            ->limit(10)
            ->get();

        $this->chartDataArray = [
            'labels'     => $products->pluck('product_name')->toArray(),
            'profit'     => $products->pluck('total_profit')->map(fn ($val) => round($val / 100, 2))->toArray(),
            'quantity'   => $products->pluck('total_quantity')->map(fn ($val) => (int) $val)->toArray(),
            'avg_margin' => $products->pluck('avg_margin_per_unit')->map(fn ($val) => round($val / 100, 2))->toArray(),
        ];
    }

    public function placeholder(): View
    {
        return view('livewire.reports.placeholders.most-profitable-products');
    }

    public function render(): View
    {
        return view('livewire.reports.most-profitable-products');
    }

    private function getPeriodConfig(): array
    {
        return match ($this->period) {
            'today' => [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay(),
            ],
            'yesterday' => [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay(),
            ],
            'last_7_days' => [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
            'last_week' => [
                Carbon::now()->subWeek()->startOfWeek()->startOfDay(),
                Carbon::now()->subWeek()->endOfWeek()->endOfDay(),
            ],
            'this_month' => [
                Carbon::now()->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ],
            'last_month' => [
                Carbon::now()->subMonth()->startOfMonth()->startOfDay(),
                Carbon::now()->subMonth()->endOfMonth()->endOfDay(),
            ],
            'last_6_months' => [
                Carbon::now()->subMonths(5)->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ],
            'this_year' => [
                Carbon::now()->startOfYear()->startOfDay(),
                Carbon::now()->endOfYear()->endOfDay(),
            ],
            'last_year' => [
                Carbon::now()->subYear()->startOfYear()->startOfDay(),
                Carbon::now()->subYear()->endOfYear()->endOfDay(),
            ],
            default => [
                Carbon::now()->subDays(29)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
        };
    }
}
