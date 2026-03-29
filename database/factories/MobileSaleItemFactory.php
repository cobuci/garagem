<?php

namespace Database\Factories;

use App\Models\MobileSale;
use App\Models\MobileSaleItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileSaleItem>
 */
class MobileSaleItemFactory extends Factory
{
    public function definition(): array
    {
        $unitPriceCents = $this->faker->numberBetween(100, 10000);
        $quantity = $this->faker->numberBetween(1, 5);

        return [
            'mobile_sale_id'   => MobileSale::factory(),
            'product_id'       => Product::factory(),
            'unit_price_cents' => $unitPriceCents,
            'quantity'         => $quantity,
            'subtotal_cents'   => $unitPriceCents * $quantity,
        ];
    }
}
