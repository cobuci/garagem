<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        User::factory()->create([
            'name'  => 'Victor Cobuci',
            'email' => 'cobuci80@gmail.com',
        ])->assignRole('admin');
    }
}
