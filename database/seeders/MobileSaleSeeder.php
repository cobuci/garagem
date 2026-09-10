<?php

namespace Database\Seeders;

use App\Enums\MobileSaleStatus;
use App\Models\Customer;
use App\Models\MobileSale;
use App\Models\MobileSaleItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MobileSaleSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        if ($products->isEmpty()) {
            $products = Product::factory()->count(10)->create();
        }

        $customers = Customer::all();
        if ($customers->isEmpty()) {
            $customers = Customer::factory()->count(5)->create();
        }

        $definitions = [
            // Pending sales from today
            ['status' => MobileSaleStatus::Pending, 'date' => Carbon::now()->subMinutes(12), 'customer' => $customers->random(), 'items_count' => 3],
            ['status' => MobileSaleStatus::Pending, 'date' => Carbon::now()->subMinutes(35), 'customer' => null, 'custom_name' => 'Oficina Mecânica São José', 'items_count' => 2],
            ['status' => MobileSaleStatus::Pending, 'date' => Carbon::now()->subHours(2), 'customer' => $customers->random(), 'items_count' => 4],
            ['status' => MobileSaleStatus::Pending, 'date' => Carbon::now()->subHours(4), 'customer' => null, 'custom_name' => 'Auto Elétrica do João', 'items_count' => 1],
            ['status' => MobileSaleStatus::Pending, 'date' => Carbon::now()->subHours(6), 'customer' => $customers->random(), 'items_count' => 5],

            // Pending sales from yesterday
            ['status' => MobileSaleStatus::Pending, 'date' => Carbon::yesterday()->setHour(16)->setMinute(20), 'customer' => $customers->random(), 'items_count' => 2],
            ['status' => MobileSaleStatus::Pending, 'date' => Carbon::yesterday()->setHour(14)->setMinute(10), 'customer' => null, 'custom_name' => 'Consumidor Balcão Rápido', 'items_count' => 3],
            ['status' => MobileSaleStatus::Pending, 'date' => Carbon::yesterday()->setHour(10)->setMinute(45), 'customer' => $customers->random(), 'items_count' => 2],

            // Synced sales (already imported to POS)
            ['status' => MobileSaleStatus::Synced, 'date' => Carbon::now()->subHours(3), 'customer' => $customers->random(), 'items_count' => 2],
            ['status' => MobileSaleStatus::Synced, 'date' => Carbon::now()->subHours(5), 'customer' => $customers->random(), 'items_count' => 4],
            ['status' => MobileSaleStatus::Synced, 'date' => Carbon::yesterday()->setHour(17)->setMinute(30), 'customer' => null, 'custom_name' => 'Retífica Central', 'items_count' => 3],
            ['status' => MobileSaleStatus::Synced, 'date' => Carbon::yesterday()->setHour(11)->setMinute(15), 'customer' => $customers->random(), 'items_count' => 1],
            ['status' => MobileSaleStatus::Synced, 'date' => Carbon::now()->subDays(2)->setHour(15)->setMinute(0), 'customer' => $customers->random(), 'items_count' => 3],
            ['status' => MobileSaleStatus::Synced, 'date' => Carbon::now()->subDays(3)->setHour(9)->setMinute(30), 'customer' => $customers->random(), 'items_count' => 2],

            // Failed sales (to test error states and retries/cancellations)
            ['status' => MobileSaleStatus::Failed, 'date' => Carbon::now()->subHours(1), 'customer' => null, 'custom_name' => 'Posto & Conveniência Alvorada', 'items_count' => 2],
            ['status' => MobileSaleStatus::Failed, 'date' => Carbon::yesterday()->setHour(18)->setMinute(5), 'customer' => $customers->random(), 'items_count' => 1],
            ['status' => MobileSaleStatus::Failed, 'date' => Carbon::now()->subDays(2)->setHour(14)->setMinute(22), 'customer' => null, 'custom_name' => 'Transportadora Vale', 'items_count' => 3],

            // Older pending sales to test pagination
            ['status' => MobileSaleStatus::Pending, 'date' => Carbon::now()->subDays(3)->setHour(16)->setMinute(0), 'customer' => $customers->random(), 'items_count' => 2],
            ['status' => MobileSaleStatus::Pending, 'date' => Carbon::now()->subDays(4)->setHour(10)->setMinute(15), 'customer' => null, 'custom_name' => 'Funilaria Express', 'items_count' => 1],
            ['status' => MobileSaleStatus::Pending, 'date' => Carbon::now()->subDays(5)->setHour(15)->setMinute(40), 'customer' => $customers->random(), 'items_count' => 4],
        ];

        foreach ($definitions as $def) {
            $selectedProducts = $products->random(min($def['items_count'], $products->count()));
            $itemsData = [];
            $totalAmountCents = 0;

            foreach ($selectedProducts as $prod) {
                $qty = rand(1, 4);
                $unitPrice = (int) ($prod->getRawOriginal('sale_price') ?: rand(1500, 18000));
                $subtotal = $unitPrice * $qty;
                $totalAmountCents += $subtotal;

                $itemsData[] = [
                    'product_id'       => $prod->id,
                    'unit_price_cents' => $unitPrice,
                    'quantity'         => $qty,
                    'subtotal_cents'   => $subtotal,
                ];
            }

            $sale = MobileSale::create([
                'local_id'           => (string) Str::uuid(),
                'customer_id'        => $def['customer']?->id,
                'customer_name'      => $def['customer'] ? null : ($def['custom_name'] ?? 'Cliente Mobile'),
                'total_amount_cents' => $totalAmountCents,
                'status'             => $def['status'],
                'device_created_at'  => $def['date'],
                'created_at'         => $def['date'],
                'updated_at'         => $def['date'],
            ]);

            foreach ($itemsData as $item) {
                MobileSaleItem::create(array_merge($item, [
                    'mobile_sale_id' => $sale->id,
                    'created_at'     => $def['date'],
                    'updated_at'     => $def['date'],
                ]));
            }
        }
    }
}
