<?php

namespace Database\Factories;

use App\Models\Changelog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Changelog>
 */
class ChangelogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $minor = 0;

        return [
            'version'     => '1.' . ($minor++) . '.0',
            'title'       => fake()->sentence(4),
            'released_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
