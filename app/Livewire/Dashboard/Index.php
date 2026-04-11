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
                'sales'                   => 0,
                'previous_sales'          => 0,
                'total'                   => 0,
                'percent'                 => 0,
                'profit'                  => 0,
                'total_profit'            => 0,
                'previous_profit'         => 0,
                'pending_sales'           => 0,
                'previous_sales_total'    => 0,
                'previous_pending_sales'  => 0,
                'pending_profit'          => 0,
                'previous_profit_total'   => 0,
                'previous_pending_profit' => 0,
            ];
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $salesMetrics = Sale::query()
            ->selectRaw('SUM(CASE WHEN DATE(created_at) = ? AND status = ? THEN net_amount ELSE 0 END) as today_sales', [$today->toDateString(), SaleStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN DATE(created_at) = ? AND status = ? THEN net_amount ELSE 0 END) as yesterday_sales', [$yesterday->toDateString(), SaleStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN DATE(created_at) = ? AND status = ? THEN net_amount ELSE 0 END) as today_pending', [$today->toDateString(), SaleStatus::Pending->value])
            ->selectRaw('SUM(CASE WHEN DATE(created_at) = ? AND status = ? THEN net_amount ELSE 0 END) as yesterday_pending', [$yesterday->toDateString(), SaleStatus::Pending->value])
            ->whereIn(DB::raw('DATE(created_at)'), [$today->toDateString(), $yesterday->toDateString()])
            ->whereIn('status', [SaleStatus::Paid, SaleStatus::Pending])
            ->first();

        $itemCosts = SaleItem::query()
            ->selectRaw('sale_id, SUM(unit_cost * quantity) as total_cost')
            ->groupBy('sale_id');

        $profitMetrics = Sale::query()
            ->joinSub($itemCosts, 'item_costs', 'sales.id', '=', 'item_costs.sale_id')
            ->selectRaw('SUM(CASE WHEN DATE(sales.created_at) = ? AND sales.status = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as today_profit', [$today->toDateString(), SaleStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN DATE(sales.created_at) = ? AND sales.status = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as yesterday_profit', [$yesterday->toDateString(), SaleStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN DATE(sales.created_at) = ? AND sales.status = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as today_pending_profit', [$today->toDateString(), SaleStatus::Pending->value])
            ->selectRaw('SUM(CASE WHEN DATE(sales.created_at) = ? AND sales.status = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as yesterday_pending_profit', [$yesterday->toDateString(), SaleStatus::Pending->value])
            ->whereIn(DB::raw('DATE(sales.created_at)'), [$today->toDateString(), $yesterday->toDateString()])
            ->whereIn('sales.status', [SaleStatus::Paid, SaleStatus::Pending])
            ->first();

        $salesToday = $salesMetrics->today_sales ?? 0;
        $salesYesterday = $salesMetrics->yesterday_sales ?? 0;
        $pendingToday = $salesMetrics->today_pending ?? 0;
        $pendingYesterday = $salesMetrics->yesterday_pending ?? 0;
        $profitToday = $profitMetrics->today_profit ?? 0;
        $profitYesterday = $profitMetrics->yesterday_profit ?? 0;
        $pendingProfitToday = $profitMetrics->today_pending_profit ?? 0;
        $pendingProfitYesterday = $profitMetrics->yesterday_pending_profit ?? 0;

        return [
            'sales'                   => $salesToday / 100,
            'previous_sales'          => $salesYesterday / 100,
            'total'                   => ($salesToday + $pendingToday) / 100,
            'percent'                 => $this->calculatePercentage((float) ($salesToday + $pendingToday), (float) ($salesYesterday + $pendingYesterday)),
            'profit'                  => $profitToday / 100,
            'total_profit'            => ($profitToday + $pendingProfitToday) / 100,
            'previous_profit'         => $profitYesterday / 100,
            'pending_sales'           => $pendingToday / 100,
            'previous_sales_total'    => ($salesYesterday + $pendingYesterday) / 100,
            'previous_pending_sales'  => $pendingYesterday / 100,
            'pending_profit'          => $pendingProfitToday / 100,
            'previous_profit_total'   => ($profitYesterday + $pendingProfitYesterday) / 100,
            'previous_pending_profit' => $pendingProfitYesterday / 100,
        ];
    }

    #[Computed]
    public function monthlyMetrics(): array
    {
        if (! $this->user->can(Permission::ViewFinancialTransaction->value)) {
            return [
                'sales'                   => 0,
                'previous_sales'          => 0,
                'total'                   => 0,
                'percent'                 => 0,
                'profit'                  => 0,
                'total_profit'            => 0,
                'previous_profit'         => 0,
                'pending_sales'           => 0,
                'previous_sales_total'    => 0,
                'previous_pending_sales'  => 0,
                'pending_profit'          => 0,
                'previous_profit_total'   => 0,
                'previous_pending_profit' => 0,
                'bar_paid_percentage'     => 0,
                'bar_pending_percentage'  => 0,
                'daily_average'           => 0,
            ];
        }

        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonthNoOverflow();

        $salesMetrics = Sale::query()
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? AND status = ? THEN net_amount ELSE 0 END) as current_sales', [$currentMonth->month, $currentMonth->year, SaleStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? AND status = ? THEN net_amount ELSE 0 END) as previous_sales', [$previousMonth->month, $previousMonth->year, SaleStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? AND status = ? THEN net_amount ELSE 0 END) as current_pending', [$currentMonth->month, $currentMonth->year, SaleStatus::Pending->value])
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? AND status = ? THEN net_amount ELSE 0 END) as previous_pending', [$previousMonth->month, $previousMonth->year, SaleStatus::Pending->value])
            ->whereIn('status', [SaleStatus::Paid, SaleStatus::Pending])
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
            ->selectRaw('SUM(CASE WHEN MONTH(sales.created_at) = ? AND YEAR(sales.created_at) = ? AND sales.status = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as current_profit', [$currentMonth->month, $currentMonth->year, SaleStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN MONTH(sales.created_at) = ? AND YEAR(sales.created_at) = ? AND sales.status = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as previous_profit', [$previousMonth->month, $previousMonth->year, SaleStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN MONTH(sales.created_at) = ? AND YEAR(sales.created_at) = ? AND sales.status = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as current_pending_profit', [$currentMonth->month, $currentMonth->year, SaleStatus::Pending->value])
            ->selectRaw('SUM(CASE WHEN MONTH(sales.created_at) = ? AND YEAR(sales.created_at) = ? AND sales.status = ? THEN sales.net_amount - item_costs.total_cost ELSE 0 END) as previous_pending_profit', [$previousMonth->month, $previousMonth->year, SaleStatus::Pending->value])
            ->whereIn('sales.status', [SaleStatus::Paid, SaleStatus::Pending])
            ->where(function (Builder $q) use ($currentMonth, $previousMonth) {
                $q->where(fn (Builder $sq) => $sq->whereMonth('sales.created_at', $currentMonth->month)->whereYear('sales.created_at', $currentMonth->year))
                    ->orWhere(fn (Builder $sq) => $sq->whereMonth('sales.created_at', $previousMonth->month)->whereYear('sales.created_at', $previousMonth->year));
            })
            ->first();

        $salesMonth = $salesMetrics->current_sales ?? 0;
        $salesLastMonth = $salesMetrics->previous_sales ?? 0;
        $pendingMonth = $salesMetrics->current_pending ?? 0;
        $pendingLastMonth = $salesMetrics->previous_pending ?? 0;
        $profitMonth = $profitMetrics->current_profit ?? 0;
        $profitLastMonth = $profitMetrics->previous_profit ?? 0;
        $pendingProfitMonth = $profitMetrics->current_pending_profit ?? 0;
        $pendingProfitLastMonth = $profitMetrics->previous_pending_profit ?? 0;

        $totalCurrent = $salesMonth + $pendingMonth;
        $totalPrevious = $salesLastMonth + $pendingLastMonth;
        $barMax = max($totalCurrent, $totalPrevious, 1);

        return [
            'sales'                   => $salesMonth / 100,
            'previous_sales'          => $salesLastMonth / 100,
            'total'                   => $totalCurrent / 100,
            'percent'                 => $this->calculatePercentage((float) $totalCurrent, (float) $totalPrevious),
            'profit'                  => $profitMonth / 100,
            'total_profit'            => ($profitMonth + $pendingProfitMonth) / 100,
            'previous_profit'         => $profitLastMonth / 100,
            'pending_sales'           => $pendingMonth / 100,
            'previous_sales_total'    => $totalPrevious / 100,
            'previous_pending_sales'  => $pendingLastMonth / 100,
            'pending_profit'          => $pendingProfitMonth / 100,
            'previous_profit_total'   => ($profitLastMonth + $pendingProfitLastMonth) / 100,
            'previous_pending_profit' => $pendingProfitLastMonth / 100,
            'bar_paid_percentage'     => ($salesMonth / $barMax) * 100,
            'bar_pending_percentage'  => ($totalCurrent / $barMax) * 100,
            'daily_average'           => ($totalCurrent / 100) / max(now()->day, 1),
        ];
    }

    #[Computed]
    public function chartData(): array
    {
        if (! $this->user->can(Permission::ViewFinancialTransaction->value)) {
            return [
                'labels'  => [],
                'sales'   => [],
                'profit'  => [],
                'pending' => [],
                'total'   => [],
            ];
        }

        $startDate = Carbon::now()->subMonthsNoOverflow(5)->startOfMonth();

        $salesByMonth = Sale::query()
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(net_amount) as total_sales')
            ->where('created_at', '>=', $startDate)
            ->where('status', SaleStatus::Paid)
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($item) => "{$item->getAttribute('year')}-{$item->getAttribute('month')}");

        $pendingByMonth = Sale::query()
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(net_amount) as total_pending')
            ->where('created_at', '>=', $startDate)
            ->where('status', SaleStatus::Pending)
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
        $pendingData = [];
        $totalData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonthsNoOverflow($i);
            $key = "{$date->year}-{$date->month}";

            $salesValue = round(($salesByMonth->get($key)->total_sales ?? 0) / 100, 2);
            $pendingValue = round(($pendingByMonth->get($key)->total_pending ?? 0) / 100, 2);

            $labels[] = $date->translatedFormat('M');
            $salesData[] = $salesValue;
            $profitData[] = round(($profitByMonth->get($key)->total_profit ?? 0) / 100, 2);
            $pendingData[] = $pendingValue;
            $totalData[] = $salesValue + $pendingValue;
        }

        return [
            'labels'  => $labels,
            'sales'   => $salesData,
            'profit'  => $profitData,
            'pending' => $pendingData,
            'total'   => $totalData,
        ];
    }

    #[Computed]
    public function goalMetrics(): array
    {
        $monthlyMetrics = $this->monthlyMetrics();
        $monthlySales = $monthlyMetrics['sales'];
        $pendingSales = $monthlyMetrics['pending_sales'];
        $target = $this->targetBalance;

        $percent = $target > 0 ? min(100, ($monthlySales / $target) * 100) : 0;
        $pendingPercent = $target > 0 ? min(100, (($monthlySales + $pendingSales) / $target) * 100) : 0;
        $remaining = max(0, $target - $monthlySales);

        return [
            'target'          => $target,
            'percent'         => $percent,
            'pending_percent' => $pendingPercent,
            'pending_sales'   => $pendingSales,
            'remaining'       => $remaining,
            'reached'         => $monthlySales >= $target && $target > 0,
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
