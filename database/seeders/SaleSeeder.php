<?php

namespace Database\Seeders;

use App\Models\Sale;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        Sale::factory()->count(10)->create(['created_at' => now()]);

        Sale::factory()->count(8)->create(['created_at' => Carbon::yesterday()]);

        Sale::factory()->count(20)->create(['created_at' => Carbon::now()->subMonth()]);

        Sale::factory()->count(30)->create(['created_at' => Carbon::now()->subMonths(2)]);

        Sale::factory()->count(3)->create([
            'created_at' => now(),
            'is_gift'    => true,
        ]);
    }
}
