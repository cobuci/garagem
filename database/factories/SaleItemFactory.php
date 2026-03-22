<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sale_id'    => Sale::factory(),
            'product_id' => Product::factory(),
            'quantity'   => $this->faker->numberBetween(1, 10),
            'unit_price' => $this->faker->randomFloat(2, 1, 100),
            'unit_cost'  => $this->faker->randomFloat(2, 0.5, 50),
            'subtotal'   => function (array $attributes) {
                return $attributes['unit_price'] * $attributes['quantity'];
            },
        ];
    }
}
