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

        $salesToday = Sale::whereDate('created_at', $today)
            ->where('status', SaleStatus::Paid)
            ->sum('total_amount');

        $salesYesterday = Sale::whereDate('created_at', $yesterday)
            ->where('status', SaleStatus::Paid)
            ->sum('total_amount');

        $profitToday = SaleItem::whereHas('sale', fn (Builder $q) => $q->whereDate('created_at', $today)->where('status', SaleStatus::Paid))
            ->select(DB::raw('SUM(subtotal - (unit_cost * quantity)) as profit'))
            ->first()->profit ?? 0;

        $profitYesterday = SaleItem::whereHas('sale', fn (Builder $q) => $q->whereDate('created_at', $yesterday)->where('status', SaleStatus::Paid))
            ->select(DB::raw('SUM(subtotal - (unit_cost * quantity)) as profit'))
            ->first()->profit ?? 0;

        return [
            'sales'           => $salesToday / 100,
            'previous_sales'  => $salesYesterday / 100,
            'percent'         => $this->calculatePercentage($salesToday, $salesYesterday),
            'profit'          => $profitToday / 100,
            'previous_profit' => $profitYesterday / 100,
        ];
    }

    #[Computed]
    public function monthlyMetrics(): array
    {
        $month = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $salesMonth = Sale::whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->where('status', SaleStatus::Paid)
            ->sum('total_amount');

        $salesLastMonth = Sale::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->where('status', SaleStatus::Paid)
            ->sum('total_amount');

        $profitMonth = SaleItem::whereHas('sale', fn (Builder $q) => $q->whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->where('status', SaleStatus::Paid))
            ->select(DB::raw('SUM(subtotal - (unit_cost * quantity)) as profit'))
            ->first()->profit ?? 0;

        $profitLastMonth = SaleItem::whereHas('sale', fn (Builder $q) => $q->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->where('status', SaleStatus::Paid))
            ->select(DB::raw('SUM(subtotal - (unit_cost * quantity)) as profit'))
            ->first()->profit ?? 0;

        return [
            'sales'           => $salesMonth / 100,
            'previous_sales'  => $salesLastMonth / 100,
            'percent'         => $this->calculatePercentage($salesMonth, $salesLastMonth),
            'profit'          => $profitMonth / 100,
            'previous_profit' => $profitLastMonth / 100,
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
