<?php

namespace App\Actions\Reports;

use App\Enums\SaleStatus;
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

        $paidSales = Sale::query()
            ->with('items')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', SaleStatus::Paid)
            ->get();

        $pendingSales = Sale::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('status', SaleStatus::Pending)
            ->get();

        $totalRevenue = $paidSales->sum(fn ($sale) => $sale->getRawOriginal('total_amount'));
        $totalDiscount = $paidSales->sum(fn ($sale) => $sale->getRawOriginal('discount_amount'));
        $totalFees = $paidSales->sum(fn ($sale) => $sale->getRawOriginal('fee_amount'));
        $totalCost = 0;
        foreach ($paidSales as $sale) {
            foreach ($sale->items as $item) {
                $totalCost += (int) ($item->getRawOriginal('unit_cost') ?? 0) * $item->quantity;
            }
        }

        $totalPendingAmount = $pendingSales->sum(fn ($sale) => $sale->getRawOriginal('total_amount'));

        return [
            'totalRevenue'         => $totalRevenue,
            'totalPendingAmount'   => $totalPendingAmount,
            'paidSalesCount'       => $paidSales->count(),
            'pendingSalesCount'    => $pendingSales->count(),
            'totalDiscount'        => $totalDiscount,
            'totalFees'            => $totalFees,
            'netSales'             => $totalRevenue - $totalDiscount - $totalFees - $totalCost,
            'salesByPaymentMethod' => $this->getSalesByPaymentMethod($paidSales, $pendingSales),
            'topProducts'          => $this->getTopProducts($start, $end),
            'startDate'            => $startDate,
            'endDate'              => $endDate,
            'generatedAt'          => now()->format('d/m/Y H:i'),
        ];
    }

    protected function getSalesByPaymentMethod(Collection $paidSales, Collection $pendingSales): Collection
    {
        $paid = $paidSales->groupBy(fn (Sale $sale) => $sale->payment_method->label())
            ->map(fn (Collection $group) => [
                'paid_count'     => $group->count(),
                'paid_amount'    => $group->sum(fn ($sale) => $sale->getRawOriginal('total_amount')),
                'pending_count'  => 0,
                'pending_amount' => 0,
            ]);

        $pending = $pendingSales->groupBy(fn (Sale $sale) => $sale->payment_method->label());

        foreach ($pending as $method => $group) {
            $existing = $paid->get($method, [
                'paid_count'     => 0,
                'paid_amount'    => 0,
                'pending_count'  => 0,
                'pending_amount' => 0,
            ]);

            $existing['pending_count'] = $group->count();
            $existing['pending_amount'] = $group->sum(fn ($sale) => $sale->getRawOriginal('total_amount'));
            $paid->put($method, $existing);
        }

        return $paid->sortByDesc(fn (array $data) => $data['paid_amount'] + $data['pending_amount']);
    }

    protected function getTopProducts(Carbon $start, Carbon $end): Collection
    {
        return SaleItem::query()
            ->select('product_id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(subtotal) as total_revenue')
            ->whereHas('sale', fn ($query) => $query
                ->whereBetween('created_at', [$start, $end])
                ->whereIn('status', [SaleStatus::Paid, SaleStatus::Pending]))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->limit(15)
            ->get();
    }
}
