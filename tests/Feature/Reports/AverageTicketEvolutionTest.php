<?php

namespace Tests\Feature\Reports;

use App\Enums\SaleStatus;
use App\Livewire\Reports\AverageTicketEvolution;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
});

test('it denies access to unauthorized users', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(AverageTicketEvolution::class)
        ->assertForbidden();
});

test('it renders for authorized users', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Livewire::actingAs($user)
        ->test(AverageTicketEvolution::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.reports.average-ticket-evolution');
});

test('it includes months from paid sales in the selected period', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    // Sale in current month — within last_6_months
    Sale::factory()->create([
        'status'       => SaleStatus::Paid,
        'is_gift'      => false,
        'total_amount' => 5000,
        'created_at'   => Carbon::now()->startOfMonth()->addDays(1),
    ]);

    $monthLabel = Carbon::now()->startOfMonth()->addDays(1)->format('M y');

    Livewire::actingAs($user)
        ->test(AverageTicketEvolution::class)
        ->assertSet('chartDataArray.categories', fn ($cats) => in_array($monthLabel, $cats));
});

test('it excludes months outside the selected period', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    // Sale 18 months ago — outside last_6_months and last_12_months
    $oldDate = Carbon::now()->subMonths(18)->startOfMonth()->addDays(1);
    Sale::factory()->create([
        'status'       => SaleStatus::Paid,
        'is_gift'      => false,
        'total_amount' => 5000,
        'created_at'   => $oldDate,
    ]);

    $oldMonthLabel = $oldDate->format('M y');

    Livewire::actingAs($user)
        ->test(AverageTicketEvolution::class)
        ->assertSet('chartDataArray.categories', fn ($cats) => ! in_array($oldMonthLabel, $cats));
});

test('it includes month when period is extended to cover old sale', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    // Sale 10 months ago — outside last_6_months but inside last_12_months
    $oldDate = Carbon::now()->subMonths(10)->startOfMonth()->addDays(1);
    Sale::factory()->create([
        'status'       => SaleStatus::Paid,
        'is_gift'      => false,
        'total_amount' => 3000,
        'created_at'   => $oldDate,
    ]);

    $oldMonthLabel = $oldDate->format('M y');

    Livewire::actingAs($user)
        ->test(AverageTicketEvolution::class)
        ->assertSet('chartDataArray.categories', fn ($cats) => ! in_array($oldMonthLabel, $cats))
        ->set('period', 'last_12_months')
        ->assertSet('chartDataArray.categories', fn ($cats) => in_array($oldMonthLabel, $cats));
});

test('it ignores cancelled sales', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    // Only a cancelled sale this month — should produce no data for this month
    Sale::factory()->create([
        'status'       => SaleStatus::Cancelled,
        'is_gift'      => false,
        'total_amount' => 9900,
        'created_at'   => Carbon::now()->startOfMonth()->addDays(1),
    ]);

    // Also create a paid sale in a different identifiable month to confirm series is built
    $paidDate = Carbon::now()->subMonths(2)->startOfMonth()->addDays(1);
    Sale::factory()->create([
        'status'       => SaleStatus::Paid,
        'is_gift'      => false,
        'total_amount' => 1000,
        'created_at'   => $paidDate,
    ]);

    Livewire::actingAs($user)
        ->test(AverageTicketEvolution::class)
        ->assertSet('chartDataArray.series', fn ($series) => count($series) === 1);
});

test('it ignores gift sales', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $giftDate = Carbon::now()->subMonths(1)->startOfMonth()->addDays(1);
    Sale::factory()->create([
        'status'       => SaleStatus::Paid,
        'is_gift'      => true,
        'total_amount' => 0,
        'created_at'   => $giftDate,
    ]);

    $giftMonthLabel = $giftDate->format('M y');

    Livewire::actingAs($user)
        ->test(AverageTicketEvolution::class)
        ->assertSet('chartDataArray.categories', fn ($cats) => ! in_array($giftMonthLabel, $cats));
});
