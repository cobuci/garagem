<?php

namespace Tests\Feature\Reports;

use App\Livewire\Reports\Index;
use App\Livewire\Reports\SalesByPeriod;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
});

test('it renders the reports page for authorized user', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    actingAs($user)
        ->get(route('reports.index'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

test('it denies access to the reports page for unauthorized user', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('reports.index'))
        ->assertForbidden();
});

test('it displays the report title and subtitle for authorized user', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    actingAs($user);

    Livewire::test(Index::class)
        ->assertSee(__('reports.title'))
        ->assertSee(__('reports.subtitle'))
        ->assertSee(__('reports.tabs.overview'))
        ->assertSee(__('reports.tabs.products'))
        ->assertSee(__('reports.tabs.customers'))
        ->assertSee(__('reports.tabs.inventory'))
        ->assertSeeLivewire(SalesByPeriod::class)
        ->assertDontSeeLivewire('reports.top-products')
        ->set('activeTab', 'products')
        ->assertSeeLivewire('reports.top-products')
        ->assertDontSeeLivewire(SalesByPeriod::class);
});
