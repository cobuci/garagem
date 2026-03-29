<?php

namespace Database\Factories;

use App\Enums\MobileSaleStatus;
use App\Models\Customer;
use App\Models\MobileSale;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MobileSale>
 */
class MobileSaleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'local_id'           => (string) Str::uuid(),
            'customer_id'        => null,
            'customer_name'      => $this->faker->name(),
            'total_amount_cents' => $this->faker->numberBetween(100, 50000),
            'status'             => MobileSaleStatus::Pending,
            'device_created_at'  => now(),
        ];
    }

    public function withCustomer(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_id'   => Customer::factory(),
            'customer_name' => null,
        ]);
    }
}
