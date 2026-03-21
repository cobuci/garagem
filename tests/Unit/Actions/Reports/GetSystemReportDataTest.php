<?php

namespace Tests\Unit\Actions\Reports;

use App\Actions\Reports\GetSystemReportData;
use App\Enums\SaleStatus;
use App\Enums\TransactionType;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetSystemReportDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_correct_data_for_report()
    {
        // 1. Setup Data
        $startDate = '2026-03-01';
        $endDate = '2026-03-31';

        // Product
        $product = Product::factory()->create(['unit_cost' => 50, 'sale_price' => 100]);

        // Sale 1: Paid, Pix, 100.00, no discount, no fee
        $sale1 = Sale::withoutEvents(function () {
            $sale = new Sale([
                'status'          => SaleStatus::Paid,
                'payment_method'  => 'pix',
                'total_amount'    => 100,
                'discount_amount' => 0,
                'fee_amount'      => 0,
                'net_amount'      => 100,
            ]);
            $sale->created_at = '2026-03-10 10:00:00';
            $sale->save();

            return $sale;
        });
        SaleItem::factory()->create([
            'sale_id'    => $sale1->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'unit_price' => 100,
            'unit_cost'  => 50,
            'subtotal'   => 100,
        ]);

        // Manual Transaction for Sale 1 to have full control
        $transaction1 = new FinancialTransaction([
            'type'           => TransactionType::Sale,
            'amount'         => 100,
            'reference_id'   => $sale1->id,
            'reference_type' => Sale::class,
        ]);
        $transaction1->created_at = '2026-03-10 10:00:00';
        $transaction1->transaction_date = '2026-03-10 10:00:00';
        $transaction1->save();

        // Sale 2: Pending
        Sale::withoutEvents(function () {
            $sale = new Sale([
                'status'         => SaleStatus::Pending,
                'total_amount'   => 200,
                'payment_method' => 'pix',
            ]);
            $sale->created_at = '2026-03-15 10:00:00';
            $sale->save();

            return $sale;
        });

        // Manual Transaction (Inflow)
        $transaction2 = new FinancialTransaction([
            'type'   => TransactionType::ManualAdjustment,
            'amount' => 50,
        ]);
        $transaction2->created_at = '2026-03-20 10:00:00';
        $transaction2->transaction_date = '2026-03-20 10:00:00';
        $transaction2->save();

        // Manual Transaction (Outflow - Purchase)
        $transaction3 = new FinancialTransaction([
            'type'   => TransactionType::Purchase,
            'amount' => -30,
        ]);
        $transaction3->created_at = '2026-03-25 10:00:00';
        $transaction3->transaction_date = '2026-03-25 10:00:00';
        $transaction3->save();

        // 2. Execute Action
        $action = new GetSystemReportData;
        $result = $action->execute($startDate, $endDate);

        // 3. Assertions
        // totalRevenue = sum of total_amount of PAID sales
        // We are now using getRawOriginal, so it should be 10000 (cents).
        $this->assertEquals(10000, $result['totalRevenue']);

        $this->assertEquals(10000, $result['netSales']);

        // inflow = ?
        // Current logic in calculateInflow: $t->type !== TransactionType::Purchase && $t->amount > 0
        // Transactions in period:
        // 1. Sale 1: amount=100, type=sale (INFLOW)
        // 2. Manual: amount=50, type=manual_adjustment (INFLOW)
        // 3. Purchase: amount=-30, type=purchase (OUTFLOW)

        // Expected Inflow = 10000 + 5000 = 15000
        $this->assertEquals(15000, $result['inflow']);

        // Expected Outflow = -3000
        $this->assertEquals(-3000, $result['outflow']);

        // Case Duplication Check:
        // totalRevenue (from sales) = 10000
        // inflow (from transactions) = 15000 (includes Sale 1)
        // In PDF:
        // Final Balance = inflow + outflow = 15000 - 3000 = 12000. (Correct cash flow)
        // But if someone looks at totalRevenue (10000) and expects it to be the ONLY inflow...
        // The PDF shows "Other Inflows" = max(0, inflow - totalRevenue) = 15000 - 10000 = 5000.
        // So: Sales (10000) + Other (5000) - Outflow (3000) = 12000.
        // It seems consistent IF the user understands that "Sales" comes from Sale models and "Inflow" from transactions.
    }

    public function test_it_handles_discount_and_fees()
    {
        $startDate = '2026-03-01';
        $endDate = '2026-03-31';

        Sale::withoutEvents(function () {
            $sale = new Sale([
                'status'          => SaleStatus::Paid,
                'payment_method'  => 'credit_card',
                'total_amount'    => 100, // Gross
                'discount_amount' => 10,
                'fee_amount'      => 5,
                'net_amount'      => 85,
            ]);
            $sale->created_at = '2026-03-10 10:00:00';
            $sale->save();
        });

        $action = new GetSystemReportData;
        $result = $action->execute($startDate, $endDate);

        $this->assertEquals(10000, $result['totalRevenue']);
        $this->assertEquals(1000, $result['totalDiscount']);
        $this->assertEquals(500, $result['totalFees']);
        $this->assertEquals(8500, $result['netSales']);
    }
}
