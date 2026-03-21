<?php

namespace Tests\Feature\RecentActivities;

use App\Enums\Permission;
use App\Enums\TransactionType;
use App\Livewire\RecentActivities\Index;
use App\Models\FinancialTransaction;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');
    $this->actingAs($this->user);
});

it('cannot access recent activities without permission', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)
        ->get(route('recent-activities.index'))
        ->assertForbidden();
});

it('can access recent activities with permission', function () {
    get(route('recent-activities.index'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

it('does not show adjustment buttons without create permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::ViewFinancialTransaction->value);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertDontSeeHtml('wire:click="openAdjustmentModal(\'add\')"')
        ->assertDontSeeHtml('wire:click="openAdjustmentModal(\'remove\')"');
});

it('shows adjustment buttons with create permission', function () {
    Livewire::test(Index::class)
        ->assertSee('Adicionar Saldo')
        ->assertSee('Remover Saldo');
});

it('does not show delete adjustment button without delete permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::ViewFinancialTransaction->value);

    FinancialTransaction::factory()->create([
        'type' => TransactionType::ManualAdjustment,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertDontSeeHtml('wire:click="confirmCancelAdjustment');
});

it('shows delete adjustment button with delete permission', function () {
    $transaction = FinancialTransaction::factory()->create([
        'type' => TransactionType::ManualAdjustment,
    ]);

    Livewire::test(Index::class)
        ->assertSeeHtml('wire:click="confirmCancelAdjustment(' . $transaction->id . ')');
});
