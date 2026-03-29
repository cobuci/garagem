<?php

use App\Enums\Permission;
use App\Livewire\Sales\Create;
use App\Models\AccountBalance;
use App\Models\Category;
use App\Models\Customer;
use App\Models\MobileSale;
use App\Models\MobileSaleItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(Permission::CreateSale->value);

    Setting::query()->delete();
    Setting::create([
        'credit_card_fee' => 5.0,
        'debit_card_fee'  => 2.5,
    ]);

    AccountBalance::query()->delete();
    AccountBalance::create([
        'current_balance' => 0,
        'target_balance'  => 0,
    ]);
});

test('it returns 403 when creating sales without permission', function () {
    $userWithoutPermission = User::factory()->create();

    actingAs($userWithoutPermission)
        ->get(route('sales.create'))
        ->assertForbidden();

    Livewire::actingAs($userWithoutPermission)
        ->test(Create::class)
        ->assertForbidden();
});

test('can search products by name', function () {
    $category = Category::factory()->create(['name' => 'Teste']);
    Product::factory()->create(['name' => 'Pizza de Calabresa', 'category_id' => $category->id]);
    Product::factory()->create(['name' => 'Hambúrguer', 'category_id' => $category->id]);

    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->set('search', 'Pizza')
        ->assertSet('products', function ($products) {
            return $products->count() === 1 && $products->first()->name === 'Pizza de Calabresa';
        })
        ->set('search', 'Ham')
        ->assertSet('products', function ($products) {
            return $products->count() === 1 && $products->first()->name === 'Hambúrguer';
        })
        ->set('search', 'Inexistente')
        ->assertSet('products', function ($products) {
            return $products->isEmpty();
        });
});

test('can filter products by category', function () {
    $cat1 = Category::factory()->create(['name' => 'Comida']);
    $cat2 = Category::factory()->create(['name' => 'Bebida']);
    Product::factory()->create(['name' => 'Pizza', 'category_id' => $cat1->id]);
    Product::factory()->create(['name' => 'Coca-Cola', 'category_id' => $cat2->id]);

    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->set('selectedCategoryId', $cat1->id)
        ->assertSet('products', function ($products) {
            return $products->count() === 1 && $products->first()->name === 'Pizza';
        })
        ->set('selectedCategoryId', $cat2->id)
        ->assertSet('products', function ($products) {
            return $products->count() === 1 && $products->first()->name === 'Coca-Cola';
        });
});

test('can add, update quantity, and remove items from cart', function () {
    $product = Product::factory()->create(['sale_price' => 10.00]);

    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->call('addItem', $product->id)
        ->assertSet('form.items.' . $product->id . '.quantity', 1)
        ->call('addItem', $product->id)
        ->assertSet('form.items.' . $product->id . '.quantity', 2)
        ->call('updateQuantity', $product->id, 5)
        ->assertSet('form.items.' . $product->id . '.quantity', 5)
        ->call('removeItem', $product->id)
        ->assertSet('form.items', [])
        ->call('addItem', $product->id)
        ->call('updateQuantity', $product->id, 0)
        ->assertSet('form.items', []);
});

test('cannot save sale without items', function () {
    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->call('save')
        ->assertHasNoErrors();

    assertDatabaseMissing('sales', []);
});

test('can create a sale and values are stored correctly in cents and balance is updated', function () {
    $category = Category::factory()->create(['name' => 'Teste']);
    $product = Product::factory()->create([
        'name'        => 'Produto Teste',
        'sale_price'  => 10.00,
        'unit_cost'   => 5.00,
        'category_id' => $category->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->call('addItem', $product->id)
        ->set('form.paymentMethod', 'credit_card')
        ->set('form.discountAmount', '2.00')
        ->set('form.passFeeToCustomer', false)
        ->set('form.status', 'paid')
        ->assertSet('feePercentage', 5.0)
        ->assertSet('feeAmount', 40)
        ->call('save');

    assertDatabaseHas('sales', [
        'total_amount'    => 800,
        'discount_amount' => 200,
        'fee_amount'      => 40,
        'net_amount'      => 760,
        'status'          => 'paid',
    ]);

    expect(AccountBalance::singleton()->getRawOriginal('current_balance'))->toBe(760);

    assertDatabaseHas('sale_items', [
        'product_id' => $product->id,
        'unit_price' => 1000,
        'unit_cost'  => 500,
        'subtotal'   => 1000,
    ]);
});

test('can create a sale and pass fee to customer', function () {
    $category = Category::factory()->create(['name' => 'Teste']);
    $product = Product::factory()->create([
        'sale_price'  => 10.00,
        'category_id' => $category->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->call('addItem', $product->id)
        ->set('form.paymentMethod', 'credit_card')
        ->set('form.passFeeToCustomer', true)
        ->assertSet('totalAmount', 1050)
        ->assertSet('netAmount', 1000)
        ->call('save');

    assertDatabaseHas('sales', [
        'total_amount'         => 1050,
        'fee_amount'           => 50,
        'pass_fee_to_customer' => true,
        'net_amount'           => 1000,
    ]);
});

test('can create a sale as a gift', function () {
    $category = Category::factory()->create(['name' => 'Teste']);
    $product = Product::factory()->create([
        'sale_price'     => 10.00,
        'category_id'    => $category->id,
        'stock_quantity' => 10,
    ]);

    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->call('addItem', $product->id)
        ->set('form.isGift', true)
        ->assertSet('totalAmount', 0)
        ->assertSet('netAmount', 0)
        ->call('save');

    assertDatabaseHas('sales', [
        'total_amount' => 0,
        'net_amount'   => 0,
        'is_gift'      => true,
    ]);

    assertDatabaseHas('sale_items', [
        'product_id' => $product->id,
        'unit_price' => 1000,
        'subtotal'   => 1000,
    ]);

    expect($product->fresh()->stock_quantity)->toBe(9);
});

test('can create a sale with a customer', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['sale_price' => 10.00]);

    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->call('addItem', $product->id)
        ->set('form.customerId', $customer->id)
        ->call('save');

    assertDatabaseHas('sales', [
        'customer_id' => $customer->id,
    ]);
});

test('can create a sale with debit card and verify fees', function () {
    $product = Product::factory()->create(['sale_price' => 100.00]);

    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->call('addItem', $product->id)
        ->set('form.paymentMethod', 'debit_card')
        ->set('form.passFeeToCustomer', false)
        ->assertSet('feePercentage', 2.5)
        ->assertSet('feeAmount', 250)
        ->assertSet('totalAmount', 10000)
        ->assertSet('netAmount', 9750)
        ->call('save');

    assertDatabaseHas('sales', [
        'payment_method' => 'debit_card',
        'fee_amount'     => 250,
        'net_amount'     => 9750,
    ]);
});

test('form resets paymentMethod and status to defaults after saving', function () {
    $product = Product::factory()->create(['sale_price' => 10.00]);

    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->call('addItem', $product->id)
        ->set('form.paymentMethod', 'credit_card')
        ->set('form.status', 'paid')
        ->call('save')
        ->assertSet('form.paymentMethod', 'money')
        ->assertSet('form.status', 'pending');
});

test('cannot add a product that does not exist to the cart', function () {
    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->call('addItem', 99999)
        ->assertSet('form.items', []);
});

test('products list is empty when no search or category is selected', function () {
    Product::factory()->create(['name' => 'Some Product']);

    Livewire::actingAs($this->user)
        ->test(Create::class)
        ->assertSet('search', '')
        ->assertSet('selectedCategoryId', null)
        ->assertSet('products', function ($products) {
            return $products->isEmpty();
        });
});

test('prefills cart and customer from mobile sale when mobileSaleId is passed', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['sale_price' => 25.00, 'unit_cost' => 10.00]);

    $mobileSale = MobileSale::factory()->withCustomer()->create(['customer_id' => $customer->id]);
    MobileSaleItem::factory()->create([
        'mobile_sale_id'   => $mobileSale->id,
        'product_id'       => $product->id,
        'quantity'         => 3,
        'unit_price_cents' => 999, // ignored — current price used
        'subtotal_cents'   => 2997,
    ]);

    $this->user->givePermissionTo(Permission::CreateSale->value);

    actingAs($this->user)
        ->get(route('sales.create', ['mobileSaleId' => $mobileSale->id]))
        ->assertOk()
        ->assertSee($product->name);
});

test('ignores mobileSaleId when mobile sale does not exist', function () {
    actingAs($this->user)
        ->get(route('sales.create', ['mobileSaleId' => 99999]))
        ->assertOk();
});

test('prefills cart without customer when mobile sale has no customer_id', function () {
    $product = Product::factory()->create(['sale_price' => 15.00]);
    $mobileSale = MobileSale::factory()->create(['customer_id' => null, 'customer_name' => 'Walk-in']);
    MobileSaleItem::factory()->create([
        'mobile_sale_id' => $mobileSale->id,
        'product_id'     => $product->id,
        'quantity'       => 2,
    ]);

    actingAs($this->user)
        ->get(route('sales.create', ['mobileSaleId' => $mobileSale->id]))
        ->assertOk();
});
