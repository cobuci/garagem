<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'            => $this->faker->name(),
            'brand'           => $this->faker->word(),
            'weight'          => $this->faker->word(),
            'upc'             => $this->faker->word(),
            'stock_quantity'  => $this->faker->randomNumber(),
            'unit_cost'       => $this->faker->randomNumber(),
            'sale_price'      => $this->faker->randomNumber(),
            'expiration_date' => now(),
            'created_at'      => now(),
            'updated_at'      => now(),

            'category_id' => Category::factory(),
        ];
    }
}
