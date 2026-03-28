<?php

namespace Tests\Feature\Customers;

use App\Enums\SaleStatus;
use App\Livewire\Customers\Show;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');
    $this->actingAs($this->user);
});

it('cannot render customer show page without permission', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    $this->actingAs($user)
        ->get(route('customers.show', $customer))
        ->assertForbidden();
});

it('can render customer show page with permission', function () {
    $customer = Customer::factory()->create();

    get(route('customers.show', $customer))
        ->assertOk()
        ->assertSeeLivewire(Show::class);
});

it('shows customer name and real purchase statistics', function () {
    $customer = Customer::factory()->create(['name' => 'John Doe']);

    $sale1 = Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Paid,
    ]);
    $sale1->update(['total_amount' => 150]);

    $sale2 = Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Pending,
    ]);
    $sale2->update(['total_amount' => 75]);

    Livewire::test(Show::class, ['customer' => $customer])
        ->assertSee('John Doe')
        ->assertSee('150,00')
        ->assertSee('75,00')
        ->assertSee('2');
});

it('lists customer orders ordered by status (pending first)', function () {
    $customer = Customer::factory()->create();

    $paidSale = Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Paid,
        'created_at'  => now()->subDay(),
    ]);

    $pendingSale = Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Pending,
        'created_at'  => now(),
    ]);

    $test = Livewire::test(Show::class, ['customer' => $customer]);

    $orders = $test->get('orders');
    expect($orders->first()->id)->toBe($pendingSale->id)
        ->and($orders->last()->id)->toBe($paidSale->id);
});

it('can filter customer orders', function () {
    $customer = Customer::factory()->create();

    $paidSale = Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Paid,
    ]);
    $paidSale->update(['total_amount' => 111]);

    $pendingSale = Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Pending,
    ]);
    $pendingSale->update(['total_amount' => 222]);

    $test = Livewire::test(Show::class, ['customer' => $customer])
        ->assertSee('111,00')
        ->assertSee('222,00');

    $test->set('status', 'paid');
    expect($test->get('orders')->count())->toBe(1);

    $test->set('status', 'pending');
    expect($test->get('orders')->count())->toBe(1);
});

it('paginates customer orders', function () {
    $customer = Customer::factory()->create();
    Sale::factory()->count(10)->create(['customer_id' => $customer->id]);

    $test = Livewire::test(Show::class, ['customer' => $customer]);

    expect($test->get('orders')->count())->toBe(5);
});

it('can show sale details', function () {
    $customer = Customer::factory()->create();
    $sale = Sale::factory()->create(['customer_id' => $customer->id]);

    Livewire::test(Show::class, ['customer' => $customer])
        ->call('showDetails', $sale->id)
        ->assertSet('showDetailsModal', true)
        ->assertSet('selectedSaleId', $sale->id);
});

it('can cancel a sale', function () {
    $customer = Customer::factory()->create();
    $sale = Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Pending,
    ]);

    Livewire::test(Show::class, ['customer' => $customer])
        ->call('confirmCancelSale', $sale->id)
        ->assertSet('showConfirmCancelModal', true)
        ->call('cancelSale')
        ->assertSet('showConfirmCancelModal', false);

    expect($sale->fresh()->status)->toBe(SaleStatus::Cancelled);
});

it('does not show the cancel button in the table', function () {
    $customer = Customer::factory()->create();
    Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Pending,
    ]);

    Livewire::test(Show::class, ['customer' => $customer])
        ->assertDontSeeHtml('wire:click="confirmCancelSale');
});

it('only users with DeleteCustomer permission can delete a customer', function () {
    $userWithoutPermission = User::factory()->create();
    $userWithoutPermission->assignRole('user');

    $customer = Customer::factory()->create();

    Livewire::actingAs($userWithoutPermission)
        ->test(Show::class, ['customer' => $customer])
        ->call('delete')
        ->assertForbidden();

    expect(Customer::where('id', $customer->id)->exists())->toBeTrue();

    Livewire::actingAs($this->user)
        ->test(Show::class, ['customer' => $customer])
        ->call('delete')
        ->assertRedirect(route('customers.index'));

    expect(Customer::where('id', $customer->id)->exists())->toBeFalse();
});

it('can call downloadInvoice method', function () {
    Queue::fake();
    $customer = Customer::factory()->create();
    $sale = Sale::factory()->create(['customer_id' => $customer->id]);

    Livewire::test(Show::class, ['customer' => $customer])
        ->call('downloadInvoice', $sale->id)
        ->assertStatus(200);
});

it('can download invoice png when it is ready', function () {
    Storage::fake();
    $customer = Customer::factory()->create();
    $sale = Sale::factory()->create([
        'customer_id'      => $customer->id,
        'invoice_status'   => 'ready',
        'invoice_path'     => 'invoices/ready.pdf',
        'invoice_png_path' => 'invoices/ready.png',
    ]);

    Storage::put('invoices/ready.png', 'dummy png content');

    Livewire::test(Show::class, ['customer' => $customer])
        ->call('downloadInvoicePng', $sale->id)
        ->assertFileDownloaded("{$sale->id}.png");
});

it('cannot mark a sale as paid without EditSale permission', function () {
    $userWithoutPermission = User::factory()->create();
    $userWithoutPermission->assignRole('user');

    $customer = Customer::factory()->create();
    $sale = Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Pending,
    ]);

    Livewire::actingAs($userWithoutPermission)
        ->test(Show::class, ['customer' => $customer])
        ->call('confirmMarkAsPaid', $sale->id)
        ->call('markAsPaid')
        ->assertForbidden();

    expect($sale->fresh()->status)->toBe(SaleStatus::Pending);
});

it('cannot cancel a sale without EditSale permission', function () {
    $userWithoutPermission = User::factory()->create();
    $userWithoutPermission->assignRole('user');

    $customer = Customer::factory()->create();
    $sale = Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Pending,
    ]);

    Livewire::actingAs($userWithoutPermission)
        ->test(Show::class, ['customer' => $customer])
        ->call('confirmCancelSale', $sale->id)
        ->call('cancelSale')
        ->assertForbidden();

    expect($sale->fresh()->status)->toBe(SaleStatus::Pending);
});

it('cannot mark an already paid sale as paid from customer page', function () {
    $customer = Customer::factory()->create();
    $sale = Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Paid,
    ]);

    Livewire::actingAs($this->user)
        ->test(Show::class, ['customer' => $customer])
        ->call('confirmMarkAsPaid', $sale->id)
        ->call('markAsPaid')
        ->assertHasNoErrors();

    expect($sale->fresh()->status)->toBe(SaleStatus::Paid);
});

it('cannot cancel an already cancelled sale from customer page', function () {
    $customer = Customer::factory()->create();
    $sale = Sale::factory()->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Cancelled,
    ]);

    Livewire::actingAs($this->user)
        ->test(Show::class, ['customer' => $customer])
        ->call('confirmCancelSale', $sale->id)
        ->call('cancelSale')
        ->assertHasNoErrors();

    expect($sale->fresh()->status)->toBe(SaleStatus::Cancelled);
});
