<?php

namespace Tests\Feature\Products;

use App\Livewire\Products\Purchase;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    config(['wireui.style.icon' => 'outline']);
});

test('it can record a product purchase and update stock', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'category_id'    => $category->id,
        'stock_quantity' => 10,
        'unit_cost'      => 5.00,
        'sale_price'     => 10.00,
    ]);

    $expirationDate = now()->addYear()->format('Y-m-d');
    $invoiceDate = now()->format('Y-m-d');

    Livewire::actingAs($this->user)
        ->test(Purchase::class)
        ->set('form.categoryId', $category->id)
        ->set('form.productId', $product->id)
        ->assertSet('form.salePrice', 10.00)
        ->assertSet('form.unitCost', 5.00)
        ->set('form.quantity', 5)
        ->assertSet('form.totalCost', 25.00)
        ->set('form.unitCost', 6.00)
        ->assertSet('form.totalCost', 30.00)
        ->set('form.totalCost', 40.00)
        ->assertSet('form.unitCost', 8.00)
        ->set('form.salePrice', 15.00)
        ->set('form.expirationDate', $expirationDate)
        ->set('form.invoiceDate', $invoiceDate)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('product:updated');

    $product->refresh();
    expect($product->stock_quantity)->toBe(15)
        ->and((float) $product->unit_cost)->toBe(8.0)
        ->and((float) $product->sale_price)->toBe(15.0)
        ->and($product->expiration_date->format('Y-m-d'))->toBe($expirationDate);

    $purchase = ProductPurchase::first();
    expect($purchase->product_id)->toBe($product->id)
        ->and((float) $purchase->quantity)->toBe(5.0)
        ->and((float) $purchase->unit_cost)->toBe(8.0)
        ->and((float) $purchase->total_cost)->toBe(40.0)
        ->and($purchase->invoice_date->format('Y-m-d'))->toBe($invoiceDate);
});

test('it filters products by category', function () {
    $cat1 = Category::factory()->create(['name' => 'Cat 1']);
    $cat2 = Category::factory()->create(['name' => 'Cat 2']);

    $prod1 = Product::factory()->create(['category_id' => $cat1->id, 'name' => 'Prod 1']);
    $prod2 = Product::factory()->create(['category_id' => $cat2->id, 'name' => 'Prod 2']);

    Livewire::actingAs($this->user)
        ->test(Purchase::class)
        ->set('form.categoryId', $cat1->id)
        ->assertCount('products', 1)
        ->assertSee('Prod 1')
        ->assertDontSee('Prod 2')
        ->set('form.categoryId', $cat2->id)
        ->assertCount('products', 1)
        ->assertSee('Prod 2')
        ->assertDontSee('Prod 1');
});

test('it calculates profit correctly', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'unit_cost'   => 5.00,
        'sale_price'  => 12.00,
    ]);

    Livewire::actingAs($this->user)
        ->test(Purchase::class)
        ->set('form.categoryId', $category->id)
        ->set('form.productId', $product->id)
        ->set('form.unitCost', 7.00)
        ->assertSet('profit', 5.00);
});
