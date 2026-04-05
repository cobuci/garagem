<?php

namespace Tests\Feature\Reports;

use App\Livewire\Reports\SalesByHourAndDay;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
});

test('it can render sales by hour and day component', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Livewire::actingAs($user)
        ->test(SalesByHourAndDay::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.reports.sales-by-hour-and-day');
});

test('it denies access to unauthorized users', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(SalesByHourAndDay::class)
        ->assertForbidden();
});

test('it updates chart data when period is changed', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Sale::factory()->count(5)->create([
        'created_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(SalesByHourAndDay::class)
        ->set('period', 'today')
        ->assertSet('period', 'today')
        ->assertViewHas('chartDataArray', function ($data) {
            return count($data['series']) === 24;
        });
});

test('it correctly handles chart data series', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $targetDate = Carbon::parse('2026-04-10 12:00:00');
    Carbon::setTestNow($targetDate);

    Sale::factory()->create([
        'created_at'   => now(),
        'total_amount' => 10000,
    ]);

    Livewire::actingAs($user)
        ->test(SalesByHourAndDay::class)
        ->set('period', 'today')
        ->assertViewHas('chartDataArray', function ($data) {
            return count($data['series']) === 24;
        });

    Carbon::setTestNow();
});
