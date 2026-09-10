<?php

namespace Tests\Feature\RecentActivities;

use App\Enums\Permission;
use App\Enums\TransactionType;
use App\Livewire\RecentActivities\Index;
use App\Models\AccountBalance;
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
        ->assertSee(__('finance.actions.add_balance'))
        ->assertSee(__('finance.actions.remove_balance'));
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
        ->assertSeeHtml('wire:click="confirmCancelAdjustment(' . $transaction->id . ')"');
});

it('can filter transactions by search term', function () {
    FinancialTransaction::factory()->create([
        'description' => 'Ajuste Especial Alfa',
        'type'        => TransactionType::ManualAdjustment,
    ]);

    FinancialTransaction::factory()->create([
        'description' => 'Venda de Balcão Beta',
        'type'        => TransactionType::Sale,
    ]);

    Livewire::test(Index::class)
        ->set('search', 'Alfa')
        ->assertSee('Ajuste Especial Alfa')
        ->assertDontSee('Venda de Balcão Beta');
});

it('can filter transactions by period', function () {
    FinancialTransaction::factory()->create([
        'description'      => 'Transação de Hoje',
        'transaction_date' => now(),
    ]);

    FinancialTransaction::factory()->create([
        'description'      => 'Transação Antiga',
        'transaction_date' => now()->subDays(45),
    ]);

    Livewire::test(Index::class)
        ->set('period', 'today')
        ->assertSee('Transação de Hoje')
        ->assertDontSee('Transação Antiga');
});

it('can reset all active filters', function () {
    FinancialTransaction::factory()->create([
        'description'      => 'Transação Única',
        'type'             => TransactionType::Sale,
        'transaction_date' => now(),
    ]);

    Livewire::test(Index::class)
        ->set('search', 'Inexistente')
        ->set('type', 'purchase')
        ->set('period', 'today')
        ->assertSee(__('finance.table.no_records_filtered'))
        ->call('resetFilters')
        ->assertSet('search', '')
        ->assertSet('type', 'all')
        ->assertSet('period', 'all')
        ->assertSee('Transação Única');
});

it('can fill entire balance into amount field on debit adjustment', function () {
    $balance = AccountBalance::singleton();
    $balance->current_balance = 350.50; // MoneyCast expects float/decimal and converts to cents (35050)
    $balance->save();

    Livewire::test(Index::class)
        ->call('openAdjustmentModal', 'remove')
        ->assertSee(__('finance.adjustment_modal.fill_all'))
        ->call('fillAllBalance')
        ->assertSet('amount', '350.50');
});
