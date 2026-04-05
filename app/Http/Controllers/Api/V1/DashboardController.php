<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SaleStatus;
use App\Http\Controllers\Controller;
use App\Http\Responses\Api\Concerns\HasApiResponses;
use App\Models\AccountBalance;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    use HasApiResponses;

    public function __invoke(): JsonResponse
    {
        return $this->ok('Dashboard data retrieved successfully.', [
            'balance' => $this->balanceData(),
            'daily'   => $this->dailyData(),
            'monthly' => $this->monthlyData(),
        ]);
    }

    /**
     * @return array{current_balance: int}
     */
    private function balanceData(): array
    {
        return [
            'current_balance' => (int) AccountBalance::singleton()->getRawOriginal('current_balance'),
        ];
    }

    /**
     * @return array{sales: int, pending: int}
     */
    private function dailyData(): array
    {
        $today = Carbon::today();

        $metrics = Sale::query()
            ->selectRaw('SUM(CASE WHEN status = ? THEN net_amount ELSE 0 END) as paid_sales', [SaleStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN net_amount ELSE 0 END) as pending_sales', [SaleStatus::Pending->value])
            ->whereIn('status', [SaleStatus::Paid, SaleStatus::Pending])
            ->whereDate('created_at', $today)
            ->first();

        return [
            'sales'   => (int) ($metrics->paid_sales ?? 0),
            'pending' => (int) ($metrics->pending_sales ?? 0),
        ];
    }

    /**
     * @return array{sales: int, pending: int, receivable: int, goal: int}
     */
    private function monthlyData(): array
    {
        $now = Carbon::now();

        $metrics = Sale::query()
            ->selectRaw('SUM(CASE WHEN status = ? THEN net_amount ELSE 0 END) as paid_sales', [SaleStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN net_amount ELSE 0 END) as pending_sales', [SaleStatus::Pending->value])
            ->whereIn('status', [SaleStatus::Paid, SaleStatus::Pending])
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->first();

        $paidSales = (int) ($metrics->paid_sales ?? 0);
        $pendingSales = (int) ($metrics->pending_sales ?? 0);

        return [
            'sales'      => $paidSales,
            'pending'    => $pendingSales,
            'receivable' => $paidSales + $pendingSales,
            'goal'       => (int) AccountBalance::singleton()->getRawOriginal('target_balance'),
        ];
    }
}
