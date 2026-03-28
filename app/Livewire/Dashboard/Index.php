<?php

namespace App\Livewire\Dashboard;

use App\Enums\Permission;
use App\Enums\SaleStatus;
use App\Models\AccountBalance;
use App\Models\FinancialTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public User $user;

    public float $targetBalance;

    public function mount(#[CurrentUser] User $user): void
    {
        $this->user = $user;
        $this->targetBalance = AccountBalance::singleton()->target_balance;
    }

    public function updateTargetBalance(): void
    {
        $this->authorize(Permission::ViewFinancialTransaction->value);

        AccountBalance::singleton()->update(['target_balance' => $this->targetBalance]);
    }

    #[Computed]
    public function totalBalance(): float
    {
        if (! $this->user->can(Permission::ViewFinancialTransaction->value)) {
            return 0;
        }

        return AccountBalance::singleton()->current_balance;
    }

    #[Computed]
    public function recentActivities(): Collection
    {
        if (! $this->user->can(Permission::ViewFinancialTransaction->value)) {
            return new Collection;
        }

        return FinancialTransaction::query()
            ->latest('transaction_date')
            ->latest('id')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function dailyMetrics(): array
    {
        if (! $this->user->can(Permission::ViewFinancialTransaction->value)) {
            return [
                'sales'           => 0,
                'previous_sales'  => 0,
                'percent'         => 0,
                'profit'          => 0,
                'previous_profit' => 0,
            ];
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $salesMetrics = Sale::query()
            ->selectRaw('SUM(CASE WHEN DATE(created_at) = ? THEN net_amount ELSE 0 END) as today_sales', [$today->toDateString()])
            ->selectRaw('SUM(CASE WHEN DATE(created_at) = ? THEN net_amount ELSE 0 END) as yesterday_sales', [$yesterday->toDateString()])
            ->whereIn(DB::raw('DATE(created_at)'), [$today->toDateString(), $yesterday->toDateString()])
            ->where('status', SaleStatus::Paid)
            ->first();

        $itemCosts = SaleItem::query()
            ->selectRaw('sale_id, SUM(unit_cost * quantity) as total_cost')
            ->groupBy('sale_id');

        $profitMetrics = Sale::query()
            ->joinSub($itemCosts, 'item_costs', 'sales.id', '=', 'item_costs.sale_id')
            ->selectRaw('SUM(CASE WHEN DATE(sales.created_at) = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as today_profit', [$today->toDateString()])
            ->selectRaw('SUM(CASE WHEN DATE(sales.created_at) = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as yesterday_profit', [$yesterday->toDateString()])
            ->whereIn(DB::raw('DATE(sales.created_at)'), [$today->toDateString(), $yesterday->toDateString()])
            ->where('sales.status', SaleStatus::Paid)
            ->first();

        $salesToday = $salesMetrics->today_sales ?? 0;
        $salesYesterday = $salesMetrics->yesterday_sales ?? 0;
        $profitToday = $profitMetrics->today_profit ?? 0;
        $profitYesterday = $profitMetrics->yesterday_profit ?? 0;

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
        if (! $this->user->can(Permission::ViewFinancialTransaction->value)) {
            return [
                'sales'           => 0,
                'previous_sales'  => 0,
                'percent'         => 0,
                'profit'          => 0,
                'previous_profit' => 0,
            ];
        }

        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        $salesMetrics = Sale::query()
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN net_amount ELSE 0 END) as current_sales', [$currentMonth->month, $currentMonth->year])
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN net_amount ELSE 0 END) as previous_sales', [$previousMonth->month, $previousMonth->year])
            ->where('status', SaleStatus::Paid)
            ->where(function (Builder $q) use ($currentMonth, $previousMonth) {
                $q->where(fn (Builder $sq) => $sq->whereMonth('created_at', $currentMonth->month)->whereYear('created_at', $currentMonth->year))
                    ->orWhere(fn (Builder $sq) => $sq->whereMonth('created_at', $previousMonth->month)->whereYear('created_at', $previousMonth->year));
            })
            ->first();

        $itemCosts = SaleItem::query()
            ->selectRaw('sale_id, SUM(unit_cost * quantity) as total_cost')
            ->groupBy('sale_id');

        $profitMetrics = Sale::query()
            ->joinSub($itemCosts, 'item_costs', 'sales.id', '=', 'item_costs.sale_id')
            ->selectRaw('SUM(CASE WHEN MONTH(sales.created_at) = ? AND YEAR(sales.created_at) = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as current_profit', [$currentMonth->month, $currentMonth->year])
            ->selectRaw('SUM(CASE WHEN MONTH(sales.created_at) = ? AND YEAR(sales.created_at) = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as previous_profit', [$previousMonth->month, $previousMonth->year])
            ->where('sales.status', SaleStatus::Paid)
            ->where(function (Builder $q) use ($currentMonth, $previousMonth) {
                $q->where(fn (Builder $sq) => $sq->whereMonth('sales.created_at', $currentMonth->month)->whereYear('sales.created_at', $currentMonth->year))
                    ->orWhere(fn (Builder $sq) => $sq->whereMonth('sales.created_at', $previousMonth->month)->whereYear('sales.created_at', $previousMonth->year));
            })
            ->first();

        $salesMonth = $salesMetrics->current_sales ?? 0;
        $salesLastMonth = $salesMetrics->previous_sales ?? 0;
        $profitMonth = $profitMetrics->current_profit ?? 0;
        $profitLastMonth = $profitMetrics->previous_profit ?? 0;

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
        if (! $this->user->can(Permission::ViewFinancialTransaction->value)) {
            return [
                'labels' => [],
                'sales'  => [],
                'profit' => [],
            ];
        }

        $startDate = Carbon::now()->subMonths(5)->startOfMonth();

        $salesByMonth = Sale::query()
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(net_amount) as total_sales')
            ->where('created_at', '>=', $startDate)
            ->where('status', SaleStatus::Paid)
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($item) => "{$item->getAttribute('year')}-{$item->getAttribute('month')}");

        $itemCostsByMonth = SaleItem::query()
            ->selectRaw('sale_id, SUM(unit_cost * quantity) as total_cost')
            ->groupBy('sale_id');

        $profitByMonth = Sale::query()
            ->joinSub($itemCostsByMonth, 'item_costs', 'sales.id', '=', 'item_costs.sale_id')
            ->selectRaw('MONTH(sales.created_at) as month, YEAR(sales.created_at) as year, SUM(sales.net_amount - item_costs.total_cost) as total_profit')
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
            $salesData[] = round(($salesByMonth->get($key)->total_sales ?? 0) / 100, 2);
            $profitData[] = round(($profitByMonth->get($key)->total_profit ?? 0) / 100, 2);
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

    private function calculatePercentage(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }

    public function render(): View
    {
        return view('livewire.dashboard.index');
    }
}
