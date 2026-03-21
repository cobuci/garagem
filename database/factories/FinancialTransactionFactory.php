<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type'             => $this->faker->randomElement(TransactionType::cases()),
            'amount'           => $this->faker->numberBetween(-10000, 10000),
            'description'      => $this->faker->sentence(),
            'transaction_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
