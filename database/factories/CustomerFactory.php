<?php

namespace Database\Factories;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'         => $this->faker->name(),
            'gender'       => fake()->randomElement(Gender::cases()),
            'phone'        => $this->faker->phoneNumber(),
            'email'        => $this->faker->unique()->safeEmail(),
            'zip_code'     => $this->faker->postcode(),
            'address'      => $this->faker->address(),
            'street'       => $this->faker->streetName(),
            'neighborhood' => $this->faker->word(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}
