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

        $sales = Sale::query()
            ->with('items')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', SaleStatus::Paid)
            ->get();

        $totalRevenue = $sales->sum(fn ($sale) => $sale->getRawOriginal('total_amount'));
        $totalDiscount = $sales->sum(fn ($sale) => $sale->getRawOriginal('discount_amount'));
        $totalFees = $sales->sum(fn ($sale) => $sale->getRawOriginal('fee_amount'));
        $totalCost = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $totalCost += (int) ($item->getRawOriginal('unit_cost') ?? 0) * $item->quantity;
            }
        }

        return [
            'totalRevenue'         => $totalRevenue,
            'totalDiscount'        => $totalDiscount,
            'totalFees'            => $totalFees,
            'netSales'             => $totalRevenue - $totalDiscount - $totalFees - $totalCost,
            'salesByPaymentMethod' => $this->getSalesByPaymentMethod($sales),
            'topProducts'          => $this->getTopProducts($start, $end),
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
            ->whereHas('sale', fn ($query) => $query->whereBetween('created_at', [$start, $end])->where('status', SaleStatus::Paid))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->limit(10)
            ->get();
    }
}
