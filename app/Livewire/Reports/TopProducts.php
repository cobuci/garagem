<?php

namespace App\Livewire\Reports;

use App\Enums\SaleStatus;
use App\Models\SaleItem;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TopProducts extends Component
{
    public string $period = 'last_30_days';

    #[Computed]
    public function chartData(): array
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
            ->where('sales.status', SaleStatus::Paid)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return [
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
                Carbon::today(),
                Carbon::today()->endOfDay(),
            ],
            'last_7_days' => [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
            'this_month' => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
            'last_6_months' => [
                Carbon::now()->subMonths(5)->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
            default => [
                Carbon::now()->subDays(29)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
        };
    }
}
