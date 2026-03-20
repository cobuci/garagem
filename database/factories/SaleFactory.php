<?php

namespace Database\Factories;

use App\Enums\SaleStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'total_amount'    => 0,
            'discount_amount' => $this->faker->randomElement([0, 0, 0, 5, 10]),
            'payment_method'  => $this->faker->randomElement(['credit_card', 'debit_card', 'cash', 'pix']),
            'status'          => $this->faker->randomElement([SaleStatus::Paid, SaleStatus::Paid, SaleStatus::Paid, SaleStatus::Pending, SaleStatus::Cancelled]),
            'is_gift'         => false,
            'customer_id'     => Customer::exists() ? Customer::inRandomOrder()->first()->id : null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Sale $sale) {
            $products = Product::all();

            if ($products->isEmpty()) {
                $products = Product::factory()->count(5)->create();
            }

            $itemsCount = rand(1, 5);
            $total = 0;

            for ($i = 0; $i < $itemsCount; $i++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $unitPrice = $product->sale_price;
                $unitCost = $product->unit_cost;
                $subtotal = $unitPrice * $quantity;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                    'unit_cost'  => $unitCost,
                    'subtotal'   => $subtotal,
                ]);

                $total += $subtotal;
            }

            if ($sale->is_gift) {
                $sale->update(['total_amount' => 0]);
            } else {
                $sale->update(['total_amount' => max(0, $total - $sale->discount_amount)]);
            }
        });
    }
}
