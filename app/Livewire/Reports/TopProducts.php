<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Enums\SaleStatus;
use App\Models\SaleItem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class TopProducts extends Component
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
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.subtotal) as total_revenue
            ')
            ->where('sales.created_at', '>=', $startDate)
            ->where('sales.created_at', '<=', $endDate)
            ->where('sales.status', '!=', SaleStatus::Cancelled)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $this->chartDataArray = [
            'labels'   => $products->pluck('product_name')->toArray(),
            'quantity' => $products->pluck('total_quantity')->map(fn ($val) => (int) $val)->toArray(),
            'revenue'  => $products->pluck('total_revenue')->map(fn ($val) => round($val / 100, 2))->toArray(),
        ];
    }

    public function placeholder(): View
    {
        return view('livewire.reports.placeholders.top-products');
    }

    public function render(): View
    {
        return view('livewire.reports.top-products');
    }

    private function getPeriodConfig(): array
    {
        return match ($this->period) {
            'today' => [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay(),
            ],
            'last_7_days' => [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
            'this_month' => [
                Carbon::now()->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ],
            'last_6_months' => [
                Carbon::now()->subMonths(5)->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ],
            default => [
                Carbon::now()->subDays(29)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
        };
    }
}
