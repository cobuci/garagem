<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_name'      => 'Garagem',
            'credit_card_fee' => 0,
            'debit_card_fee'  => 0,
            'address'         => $this->faker->address,
            'city'            => $this->faker->city,
            'state'           => $this->faker->streetName,
            'zip_code'        => $this->faker->postcode,
        ];
    }
}
