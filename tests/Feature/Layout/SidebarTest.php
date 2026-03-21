<?php

namespace Tests\Feature\Layout;

use App\Livewire\Layout\Sidebar;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('sidebar displays correct menu items for admin', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Livewire::actingAs($user)
        ->test(Sidebar::class)
        ->assertSee(__('sidebar.dashboard'))
        ->assertSee(__('sidebar.recent_activities'))
        ->assertSee(__('sidebar.customers'))
        ->assertSee(__('sidebar.products'))
        ->assertSee(__('sidebar.pos'))
        ->assertSee(__('sidebar.orders'))
        ->assertSee(__('sidebar.bills_payable'))
        ->assertSee(__('sidebar.reports'));
});

test('sidebar hides restricted menu items for common user', function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('user');

    Livewire::actingAs($user)
        ->test(Sidebar::class)
        ->assertSee(__('sidebar.dashboard'))
        ->assertSee(__('sidebar.customers'))
        ->assertSee(__('sidebar.products'))
        ->assertSee(__('sidebar.pos'))
        ->assertSee(__('sidebar.orders'))
        ->assertSee(__('sidebar.reports'))
        ->assertDontSee(__('sidebar.recent_activities'))
        ->assertDontSee(__('sidebar.bills_payable'));
});

test('user can switch accounts when not in production', function () {
    $user1 = User::factory()->create(['name' => 'User One']);
    $user2 = User::factory()->create(['name' => 'User Two']);

    Livewire::actingAs($user1)
        ->test(Sidebar::class)
        ->call('switchUser', $user2->id)
        ->assertRedirect();

    expect(auth()->id())->toBe($user2->id);
});

test('dashboard hides financial info for common user', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertDontSee(__('dashboard.total_balance'))
        ->assertDontSee(__('dashboard.sales_today'))
        ->assertDontSee(__('dashboard.sales_month'));
});
