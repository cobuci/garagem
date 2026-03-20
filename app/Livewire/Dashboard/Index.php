<?php

namespace App\Livewire\Dashboard;

use App\Enums\SaleStatus;
use App\Models\AccountBalance;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public float $targetBalance;

    public function mount(): void
    {
        $this->targetBalance = AccountBalance::singleton()->target_balance;
    }

    public function updateTargetBalance(): void
    {
        AccountBalance::singleton()->update(['target_balance' => $this->targetBalance]);
    }

    #[Computed]
    public function totalBalance(): float
    {
        return AccountBalance::singleton()->current_balance;
    }

    #[Computed]
    public function recentSales(): Collection
    {
        return Sale::with(['customer', 'items.product'])
            ->latest()
            ->limit(6)
            ->get();
    }

    #[Computed]
    public function dailyMetrics(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $sales = Sale::query()
            ->selectRaw('SUM(CASE WHEN DATE(created_at) = ? THEN total_amount ELSE 0 END) as today_sales', [$today->toDateString()])
            ->selectRaw('SUM(CASE WHEN DATE(created_at) = ? THEN total_amount ELSE 0 END) as yesterday_sales', [$yesterday->toDateString()])
            ->whereIn(DB::raw('DATE(created_at)'), [$today->toDateString(), $yesterday->toDateString()])
            ->where('status', SaleStatus::Paid)
            ->first();

        $profits = SaleItem::query()
            ->whereHas('sale', fn (Builder $q) => $q->whereIn(DB::raw('DATE(created_at)'), [$today->toDateString(), $yesterday->toDateString()])->where('status', SaleStatus::Paid))
            ->selectRaw('SUM(CASE WHEN DATE(sales.created_at) = ? THEN subtotal - (unit_cost * quantity) ELSE 0 END) as today_profit', [$today->toDateString()])
            ->selectRaw('SUM(CASE WHEN DATE(sales.created_at) = ? THEN subtotal - (unit_cost * quantity) ELSE 0 END) as yesterday_profit', [$yesterday->toDateString()])
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->first();

        $salesToday = $sales->today_sales ?? 0;
        $salesYesterday = $sales->yesterday_sales ?? 0;
        $profitToday = $profits->today_profit ?? 0;
        $profitYesterday = $profits->yesterday_profit ?? 0;

        return [
            'sales'           => $salesToday / 100,
            'previous_sales'  => $salesYesterday / 100,
            'percent'         => $this->calculatePercentage((float) $salesToday, (float) $salesYesterday),
            'profit'          => $profitToday / 100,
            'previous_profit' => $profitYesterday / 100,
        ];
    }

    #[Computed]
    public function monthlyMetrics(): array
    {
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        $sales = Sale::query()
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN total_amount ELSE 0 END) as current_sales', [$currentMonth->month, $currentMonth->year])
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN total_amount ELSE 0 END) as previous_sales', [$previousMonth->month, $previousMonth->year])
            ->where(function (Builder $q) use ($currentMonth, $previousMonth) {
                $q->where(fn (Builder $sq) => $sq->whereMonth('created_at', $currentMonth->month)->whereYear('created_at', $currentMonth->year))
                    ->orWhere(fn (Builder $sq) => $sq->whereMonth('created_at', $previousMonth->month)->whereYear('created_at', $previousMonth->year));
            })
            ->where('status', SaleStatus::Paid)
            ->first();

        $profits = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->selectRaw('SUM(CASE WHEN MONTH(sales.created_at) = ? AND YEAR(sales.created_at) = ? THEN subtotal - (unit_cost * quantity) ELSE 0 END) as current_profit', [$currentMonth->month, $currentMonth->year])
            ->selectRaw('SUM(CASE WHEN MONTH(sales.created_at) = ? AND YEAR(sales.created_at) = ? THEN subtotal - (unit_cost * quantity) ELSE 0 END) as previous_profit', [$previousMonth->month, $previousMonth->year])
            ->where('sales.status', SaleStatus::Paid)
            ->where(function (Builder $q) use ($currentMonth, $previousMonth) {
                $q->where(fn (Builder $sq) => $sq->whereMonth('sales.created_at', $currentMonth->month)->whereYear('sales.created_at', $currentMonth->year))
                    ->orWhere(fn (Builder $sq) => $sq->whereMonth('sales.created_at', $previousMonth->month)->whereYear('sales.created_at', $previousMonth->year));
            })
            ->first();

        $salesMonth = $sales->current_sales ?? 0;
        $salesLastMonth = $sales->previous_sales ?? 0;
        $profitMonth = $profits->current_profit ?? 0;
        $profitLastMonth = $profits->previous_profit ?? 0;

        return [
            'sales'           => $salesMonth / 100,
            'previous_sales'  => $salesLastMonth / 100,
            'percent'         => $this->calculatePercentage((float) $salesMonth, (float) $salesLastMonth),
            'profit'          => $profitMonth / 100,
            'previous_profit' => $profitLastMonth / 100,
        ];
    }

    #[Computed]
    public function chartData(): array
    {
        $startDate = Carbon::now()->subMonths(5)->startOfMonth();

        $sales = Sale::query()
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_amount) as total')
            ->where('created_at', '>=', $startDate)
            ->where('status', SaleStatus::Paid)
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($item) => "{$item->getAttribute('year')}-{$item->getAttribute('month')}");

        $profits = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->selectRaw('MONTH(sales.created_at) as month, YEAR(sales.created_at) as year, SUM(subtotal - (unit_cost * quantity)) as total')
            ->where('sales.created_at', '>=', $startDate)
            ->where('sales.status', SaleStatus::Paid)
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($item) => "{$item->getAttribute('year')}-{$item->getAttribute('month')}");

        $labels = [];
        $salesData = [];
        $profitData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = "{$date->year}-{$date->month}";

            $labels[] = $date->translatedFormat('M');
            $salesData[] = round(($sales->get($key)->total ?? 0) / 100, 2);
            $profitData[] = round(($profits->get($key)->total ?? 0) / 100, 2);
        }

        return [
            'labels' => $labels,
            'sales'  => $salesData,
            'profit' => $profitData,
        ];
    }

    #[Computed]
    public function goalMetrics(): array
    {
        $monthlyMetrics = $this->monthlyMetrics();
        $monthlySales = $monthlyMetrics['sales'];
        $target = $this->targetBalance;

        $percent = $target > 0 ? min(100, ($monthlySales / $target) * 100) : 0;
        $remaining = max(0, $target - $monthlySales);

        return [
            'target'    => $target,
            'percent'   => $percent,
            'remaining' => $remaining,
            'reached'   => $monthlySales >= $target && $target > 0,
        ];
    }

    public function render(): View
    {
        return view('livewire.dashboard.index');
    }

    private function calculatePercentage(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }
}
