<?php

use App\Livewire\Audits\Index;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use OwenIt\Auditing\Models\Audit;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    config(['audit.console' => true]);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    actingAs($this->admin);
});

it('denies access to unauthorized user', function () {
    $unauthorized = User::factory()->create();

    Livewire::actingAs($unauthorized)
        ->test(Index::class)
        ->assertForbidden();
});

it('renders the audit list for admin', function () {
    Livewire::test(Index::class)
        ->assertStatus(200)
        ->assertSee(__('audits.table.date'));
});

it('lists audit records', function () {
    Product::factory()->create();

    Livewire::test(Index::class)
        ->assertSet('audits', fn ($audits) => $audits->total() >= 1);
});

it('filters audits by event', function () {
    $customer = Customer::factory()->create();
    $customer->update(['name' => 'Updated Name']);

    Livewire::test(Index::class)
        ->call('filterByEvent', 'created')
        ->assertSet('event', 'created')
        ->assertSet('audits', fn ($audits) => $audits->every(fn ($a) => $a->event === 'created'))
        ->call('filterByEvent', 'updated')
        ->assertSet('event', 'updated')
        ->assertSet('audits', fn ($audits) => $audits->every(fn ($a) => $a->event === 'updated'));
});

it('filters audits by model', function () {
    Product::factory()->create();
    Customer::factory()->create();

    Livewire::test(Index::class)
        ->set('auditable', 'Product')
        ->assertSet('audits', fn ($audits) => $audits->every(
            fn ($a) => $a->auditable_type === Product::class,
        ));
});

it('filters audits by user', function () {
    $otherUser = User::factory()->create();

    actingAs($otherUser);
    Customer::factory()->create();

    actingAs($this->admin);

    Livewire::test(Index::class)
        ->set('userId', $otherUser->id)
        ->assertSet('audits', fn ($audits) => $audits->every(
            fn ($a) => $a->user_id === $otherUser->id,
        ));
});

it('resets event filter state on call', function () {
    Livewire::test(Index::class)
        ->call('filterByEvent', 'created')
        ->assertSet('event', 'created')
        ->call('filterByEvent', '')
        ->assertSet('event', '');
});

it('resets model filter state on set', function () {
    Livewire::test(Index::class)
        ->set('auditable', 'Product')
        ->assertSet('auditable', 'Product')
        ->set('auditable', '')
        ->assertSet('auditable', '');
});

it('opens details modal for a specific audit', function () {
    $product = Product::factory()->create();

    $audit = Audit::query()
        ->where('auditable_type', Product::class)
        ->where('auditable_id', $product->id)
        ->first();

    expect($audit)->not->toBeNull();

    Livewire::test(Index::class)
        ->call('showDetails', $audit->id)
        ->assertSet('selectedAuditId', $audit->id)
        ->assertSet('showDetailsModal', true)
        ->assertSet('selectedAudit.id', $audit->id);
});

it('shows all events when filter is cleared', function () {
    Product::factory()->create();
    $product = Product::factory()->create();
    $product->update(['name' => 'Changed']);

    Livewire::test(Index::class)
        ->call('filterByEvent', '')
        ->assertSet('event', '')
        ->assertSet('audits', fn ($audits) => $audits->total() >= 2);
});
