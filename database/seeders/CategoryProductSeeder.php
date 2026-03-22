<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        $icons = ['tag', 'shopping-cart', 'beaker', 'home', 'truck', 'gift', 'puzzle-piece', 'identification', 'briefcase', 'archive-box'];

        Category::factory(10)
            ->sequence(fn ($sequence) => [
                'sort_order' => $sequence->index,
                'icon'       => $icons[$sequence->index % count($icons)],
            ])
            ->has(Product::factory(20))
            ->create();
    }
}
