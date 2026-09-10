<?php

use App\Enums\MobileSaleStatus;
use App\Enums\Permission;
use App\Livewire\MobileSales\Index;
use App\Models\MobileSale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(Permission::ViewSale->value);
});

test('it returns 403 when viewing mobile sales without permission', function () {
    $userWithoutPermission = User::factory()->create();

    actingAs($userWithoutPermission)
        ->get(route('mobile-sales.index'))
        ->assertForbidden();

    Livewire::actingAs($userWithoutPermission)
        ->test(Index::class)
        ->assertForbidden();
});

test('authenticated user with permission can access mobile sales page', function () {
    actingAs($this->user)
        ->get(route('mobile-sales.index'))
        ->assertOk();
});

test('lists pending mobile sales by default', function () {
    MobileSale::factory()->create(['status' => MobileSaleStatus::Pending]);
    MobileSale::factory()->create(['status' => MobileSaleStatus::Synced]);
    MobileSale::factory()->create(['status' => MobileSaleStatus::Failed]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSet('status', 'pending')
        ->assertSet('mobileSales', fn ($sales) => $sales->count() === 1);
});

test('can show mobile sale details', function () {
    $sale = MobileSale::factory()->create();

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('showDetails', $sale->id)
        ->assertSet('selectedMobileSaleId', $sale->id)
        ->assertSet('showDetailsModal', true)
        ->assertSet('selectedMobileSale.id', $sale->id);
});

test('selected mobile sale is null when no sale is selected', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSet('selectedMobileSaleId', null)
        ->assertSet('selectedMobileSale', null);
});

test('totalSales counts all mobile sales', function () {
    MobileSale::factory()->count(2)->create(['status' => MobileSaleStatus::Pending]);
    MobileSale::factory()->create(['status' => MobileSaleStatus::Synced]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSet('totalSales', 3);
});

test('totalPending counts only pending mobile sales', function () {
    MobileSale::factory()->count(2)->create(['status' => MobileSaleStatus::Pending]);
    MobileSale::factory()->create(['status' => MobileSaleStatus::Synced]);
    MobileSale::factory()->create(['status' => MobileSaleStatus::Failed]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSet('totalPending', 2);
});

test('totalSynced counts only synced mobile sales', function () {
    MobileSale::factory()->count(2)->create(['status' => MobileSaleStatus::Pending]);
    MobileSale::factory()->count(3)->create(['status' => MobileSaleStatus::Synced]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSet('totalSynced', 3);
});

test('totalPendingAmount calculates pending amounts in reais', function () {
    MobileSale::factory()->create(['status' => MobileSaleStatus::Pending, 'total_amount_cents' => 15000]);
    MobileSale::factory()->create(['status' => MobileSaleStatus::Pending, 'total_amount_cents' => 25000]);
    MobileSale::factory()->create(['status' => MobileSaleStatus::Synced, 'total_amount_cents' => 10000]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSet('totalPendingAmount', 400.0);
});

test('can filter mobile sales by status', function () {
    MobileSale::factory()->count(2)->create(['status' => MobileSaleStatus::Pending]);
    MobileSale::factory()->count(3)->create(['status' => MobileSaleStatus::Synced]);
    MobileSale::factory()->create(['status' => MobileSaleStatus::Failed]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('filterByStatus', 'pending')
        ->assertSet('status', 'pending')
        ->assertSet('mobileSales', fn ($sales) => $sales->count() === 2)
        ->call('filterByStatus', 'synced')
        ->assertSet('status', 'synced')
        ->assertSet('mobileSales', fn ($sales) => $sales->count() === 3)
        ->call('filterByStatus', null)
        ->assertSet('status', null)
        ->assertSet('mobileSales', fn ($sales) => $sales->count() === 6);
});

test('can search mobile sales by customer name or local_id', function () {
    $sale1 = MobileSale::factory()->create(['customer_name' => 'Oficina Mecanica Central', 'local_id' => 'uuid-123']);
    $sale2 = MobileSale::factory()->create(['customer_name' => 'Auto Pecas Silva', 'local_id' => 'uuid-456']);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('search', 'Oficina')
        ->assertSet('mobileSales', fn ($sales) => $sales->count() === 1 && $sales->first()->id === $sale1->id)
        ->set('search', 'uuid-456')
        ->assertSet('mobileSales', fn ($sales) => $sales->count() === 1 && $sales->first()->id === $sale2->id)
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('status', 'pending')
        ->assertSet('mobileSales', fn ($sales) => $sales->count() === 2);
});

test('mobile sales are ordered by device_created_at descending', function () {
    $older = MobileSale::factory()->create(['device_created_at' => now()->subDay()]);
    $newer = MobileSale::factory()->create(['device_created_at' => now()]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSet('mobileSales', function ($sales) use ($newer, $older) {
            return $sales->first()->id === $newer->id
                && $sales->last()->id === $older->id;
        });
});

describe('delete', function () {
    beforeEach(function () {
        $this->user->givePermissionTo(Permission::DeleteSale->value);
    });

    test('user without delete permission gets 403', function () {
        $userWithoutPermission = User::factory()->create();
        $userWithoutPermission->givePermissionTo(Permission::ViewSale->value);
        $sale = MobileSale::factory()->create(['status' => MobileSaleStatus::Pending]);

        Livewire::actingAs($userWithoutPermission)
            ->test(Index::class)
            ->call('delete', $sale->id)
            ->assertForbidden();

        expect(MobileSale::find($sale->id))->not->toBeNull();
    });

    test('can delete a pending mobile sale', function () {
        $sale = MobileSale::factory()->create(['status' => MobileSaleStatus::Pending]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->call('showDetails', $sale->id)
            ->call('delete', $sale->id)
            ->assertSet('showDetailsModal', false)
            ->assertSet('selectedMobileSaleId', null);

        expect(MobileSale::find($sale->id))->toBeNull()
            ->and(MobileSale::withTrashed()->find($sale->id))->not->toBeNull();
    });

    test('cannot delete a synced mobile sale', function () {
        $sale = MobileSale::factory()->create(['status' => MobileSaleStatus::Synced]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->call('delete', $sale->id);

        expect(MobileSale::find($sale->id))->not->toBeNull();
    });

    test('deleted sale is removed from the list', function () {
        $sale = MobileSale::factory()->create(['status' => MobileSaleStatus::Pending]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->assertSet('totalSales', 1)
            ->call('delete', $sale->id)
            ->assertSet('totalSales', 0)
            ->assertSet('mobileSales', fn ($sales) => $sales->count() === 0);
    });
});
