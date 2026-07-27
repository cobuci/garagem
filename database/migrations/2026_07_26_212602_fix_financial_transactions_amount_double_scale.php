<?php

use App\Models\ProductPurchase;
use App\Models\Sale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->hasDoubleScaledAmounts()) {
            return;
        }

        DB::table('financial_transactions')
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('financial_transactions')
                        ->where('id', $row->id)
                        ->update(['amount' => (int) round($row->amount / 100)]);
                }
            });
    }

    public function down(): void
    {
        if ($this->hasDoubleScaledAmounts()) {
            return;
        }

        $hasCorrectlyScaledReference = DB::table('financial_transactions as ft')
            ->join('sales as s', function ($join): void {
                $join->on('ft.reference_id', '=', 's.id')
                    ->where('ft.reference_type', '=', Sale::class);
            })
            ->whereRaw('ABS(ft.amount) = ABS(s.net_amount)')
            ->exists();

        if (! $hasCorrectlyScaledReference) {
            return;
        }

        DB::table('financial_transactions')
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('financial_transactions')
                        ->where('id', $row->id)
                        ->update(['amount' => (int) $row->amount * 100]);
                }
            });
    }

    private function hasDoubleScaledAmounts(): bool
    {
        $saleMismatch = DB::table('financial_transactions as ft')
            ->join('sales as s', function ($join): void {
                $join->on('ft.reference_id', '=', 's.id')
                    ->where('ft.reference_type', '=', Sale::class);
            })
            ->where('s.net_amount', '!=', 0)
            ->whereRaw('ABS(ft.amount) = ABS(s.net_amount) * 100')
            ->exists();

        if ($saleMismatch) {
            return true;
        }

        return DB::table('financial_transactions as ft')
            ->join('product_purchases as p', function ($join): void {
                $join->on('ft.reference_id', '=', 'p.id')
                    ->where('ft.reference_type', '=', ProductPurchase::class);
            })
            ->where('p.total_cost', '!=', 0)
            ->whereRaw('ABS(ft.amount) = ABS(p.total_cost) * 100')
            ->exists();
    }
};
