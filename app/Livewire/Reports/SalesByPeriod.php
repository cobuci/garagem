<?php

namespace App\Livewire\Reports;

use App\Enums\SaleStatus;
use App\Models\SaleItem;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SalesByPeriod extends Component
{
    public string $period = 'last_30_days';

    #[Computed]
    public function chartData(): array
    {
        [$startDate, $endDate, $groupBy] = $this->getPeriodConfig();

        $metrics = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->selectRaw($this->getSelectRaw($groupBy))
            ->where('sales.created_at', '>=', $startDate)
            ->where('sales.created_at', '<=', $endDate)
            ->where('sales.status', SaleStatus::Paid)
            ->groupBy('period_label', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        return [
            'labels' => $metrics->pluck('period_label')->toArray(),
            'sales'  => $metrics->pluck('total_sales')->map(fn ($val) => round($val / 100, 2))->toArray(),
            'profit' => $metrics->pluck('total_profit')->map(fn ($val) => round($val / 100, 2))->toArray(),
        ];
    }

    public function placeholder(): View
    {
        return view('livewire.reports.placeholders.sales-by-period');
    }

    public function render(): View
    {
        return view('livewire.reports.sales-by-period');
    }

    private function getPeriodConfig(): array
    {
        return match ($this->period) {
            'today' => [
                Carbon::today(),
                Carbon::today()->endOfDay(),
                'hour',
            ],
            'last_7_days' => [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay(),
                'day',
            ],
            'last_30_days' => [
                Carbon::now()->subDays(29)->startOfDay(),
                Carbon::now()->endOfDay(),
                'day',
            ],
            'this_month' => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
                'day',
            ],
            'last_6_months' => [
                Carbon::now()->subMonths(5)->startOfMonth(),
                Carbon::now()->endOfMonth(),
                'month',
            ],
            default => [
                Carbon::now()->subDays(29)->startOfDay(),
                Carbon::now()->endOfDay(),
                'day',
            ],
        };
    }

    private function getSelectRaw(string $groupBy): string
    {
        $totalSales = 'SUM(subtotal) as total_sales';
        $totalProfit = 'SUM(subtotal - (unit_cost * quantity)) as total_profit';

        return match ($groupBy) {
            'hour'  => "DATE_FORMAT(sales.created_at, '%H:00') as period_label, DATE_FORMAT(sales.created_at, '%H') as sort_key, {$totalSales}, {$totalProfit}",
            'day'   => "DATE_FORMAT(sales.created_at, '%d/%m') as period_label, DATE(sales.created_at) as sort_key, {$totalSales}, {$totalProfit}",
            'month' => "DATE_FORMAT(sales.created_at, '%m/%Y') as period_label, DATE_FORMAT(sales.created_at, '%Y-%m') as sort_key, {$totalSales}, {$totalProfit}",
        };
    }
}
