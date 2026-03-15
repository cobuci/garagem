<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'            => $this->faker->words(3, true),
            'brand'           => $this->faker->company(),
            'weight'          => $this->faker->numberBetween(100, 2000) . 'g',
            'upc'             => $this->faker->unique()->ean13(),
            'stock_quantity'  => $this->faker->numberBetween(0, 100),
            'unit_cost'       => $this->faker->randomFloat(2, 1, 50),
            'sale_price'      => $this->faker->randomFloat(2, 51, 150),
            'expiration_date' => $this->faker->dateTimeBetween('now', '+2 years'),
            'created_at'      => now(),
            'updated_at'      => now(),

            'category_id' => Category::factory(),
        ];
    }
}
