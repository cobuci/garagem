<?php

namespace Database\Factories;

use App\Models\Changelog;
use App\Models\ChangelogItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChangelogItem>
 */
class ChangelogItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'changelog_id' => Changelog::factory(),
            'title'        => fake()->sentence(3),
            'description'  => fake()->paragraph(),
            'image_path'   => null,
            'sort_order'   => fake()->numberBetween(0, 10),
        ];
    }
}
