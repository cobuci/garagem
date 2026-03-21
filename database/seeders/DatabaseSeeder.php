<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // User::factory(10)->create();

        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ])->assignRole('admin');

        User::factory()->create([
            'name'  => 'Common User',
            'email' => 'user@example.com',
        ])->assignRole('user');

        $this->call([
            CustomerSeeder::class,
            CategoryProductSeeder::class,
            SaleSeeder::class,
        ]);
    }
}
