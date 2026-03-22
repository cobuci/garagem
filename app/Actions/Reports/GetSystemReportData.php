<?php

namespace App\Actions\Reports;

use App\Enums\TransactionType;
use App\Models\FinancialTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GetSystemReportData
{
    public function execute(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $sales = Sale::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'paid')
            ->get();

        $transactions = FinancialTransaction::query()
            ->whereBetween('transaction_date', [$start, $end])
            ->get();

        return [
            'totalRevenue'         => $sales->sum(fn ($sale) => $sale->getRawOriginal('total_amount')),
            'totalDiscount'        => $sales->sum(fn ($sale) => $sale->getRawOriginal('discount_amount')),
            'totalFees'            => $sales->sum(fn ($sale) => $sale->getRawOriginal('fee_amount')),
            'netSales'             => $sales->sum(fn ($sale) => $sale->getRawOriginal('net_amount')),
            'salesByPaymentMethod' => $this->getSalesByPaymentMethod($sales),
            'topProducts'          => $this->getTopProducts($start, $end),
            'inflow'               => $this->calculateInflow($transactions),
            'outflow'              => $this->calculateOutflow($transactions),
            'startDate'            => $startDate,
            'endDate'              => $endDate,
            'generatedAt'          => now()->format('d/m/Y H:i'),
        ];
    }

    protected function getSalesByPaymentMethod(Collection $sales): Collection
    {
        return $sales->groupBy('payment_method')
            ->map(fn (Collection $group) => [
                'count'  => $group->count(),
                'amount' => $group->sum(fn ($sale) => $sale->getRawOriginal('total_amount')),
            ]);
    }

    protected function getTopProducts(Carbon $start, Carbon $end): Collection
    {
        return SaleItem::query()
            ->select('product_id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(subtotal) as total_revenue')
            ->whereHas('sale', fn ($query) => $query->whereBetween('created_at', [$start, $end])->where('status', 'paid'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->limit(10)
            ->get();
    }

    protected function calculateInflow(Collection $transactions): int|float
    {
        return $transactions
            ->filter(fn (FinancialTransaction $t) => $t->type !== TransactionType::Purchase && $t->getRawOriginal('amount') > 0)
            ->sum(fn ($t) => $t->getRawOriginal('amount'));
    }

    protected function calculateOutflow(Collection $transactions): int|float
    {
        return $transactions
            ->filter(fn (FinancialTransaction $t) => $t->type === TransactionType::Purchase || $t->getRawOriginal('amount') < 0)
            ->sum(fn ($t) => $t->getRawOriginal('amount'));
    }
}
