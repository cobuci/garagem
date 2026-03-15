<?php

use App\Livewire\Products\Edit;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    config(['wireui.style.icon' => 'outline']);
});

test('it can edit a product with all fields including extra ones', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'category_id'     => $category->id,
        'name'            => 'Old Name',
        'brand'           => 'Old Brand',
        'weight'          => '100g',
        'upc'             => 'OLD_UPC',
        'unit_cost'       => 10,
        'sale_price'      => 20,
        'stock_quantity'  => 5,
        'expiration_date' => now()->addYear()->startOfDay(),
    ]);

    $newCategory = Category::factory()->create();
    $expirationDate = now()->addYears(2)->format('Y-m-d');

    Livewire::actingAs($this->user)
        ->test(Edit::class)
        ->dispatch('product:edit', product: $product->id)
        ->assertSet('editDrawer', true)
        ->assertSet('form.name', 'Old Name')
        ->assertSet('form.unitCost', 10)
        ->set('form.categoryId', $newCategory->id)
        ->set('form.name', 'Updated Name')
        ->set('form.brand', 'Updated Brand')
        ->set('form.weightValue', 250.5)
        ->set('form.weightType', 'kg')
        ->set('form.upc', 'NEW_UPC')
        ->set('form.unitCost', 15.50)
        ->set('form.salePrice', 3075.99)
        ->set('form.stockQuantity', 50)
        ->set('form.expirationDate', $expirationDate)
        ->call('update')
        ->assertHasNoErrors()
        ->assertSet('editDrawer', false)
        ->assertDispatched('product:updated');

    $product->refresh();
    expect($product->name)->toBe('Updated Name')
        ->and($product->category_id)->toBe($newCategory->id)
        ->and($product->brand)->toBe('Updated Brand')
        ->and($product->weight)->toBe('250.5kg')
        ->and($product->upc)->toBe('NEW_UPC')
        ->and((float) $product->unit_cost)->toBe(15.5)
        ->and((float) $product->sale_price)->toBe(3075.99)
        ->and($product->stock_quantity)->toBe(50)
        ->and($product->expiration_date->format('Y-m-d'))->toBe($expirationDate);
});

test('it validates unique upc excluding current product', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['upc' => 'OTHER_UPC', 'category_id' => $category->id]);
    $product = Product::factory()->create(['upc' => 'MY_UPC', 'category_id' => $category->id]);

    Livewire::actingAs($this->user)
        ->test(Edit::class)
        ->dispatch('product:edit', product: $product->id)
        ->set('form.upc', 'OTHER_UPC')
        ->call('update')
        ->assertHasErrors(['form.upc' => 'unique'])
        ->set('form.upc', 'MY_UPC')
        ->call('update')
        ->assertHasNoErrors();
});

test('it validates extra fields numeric and positive', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);

    Livewire::actingAs($this->user)
        ->test(Edit::class)
        ->dispatch('product:edit', product: $product->id)
        ->set('form.unitCost', 'not-numeric')
        ->set('form.salePrice', -10)
        ->set('form.stockQuantity', -5)
        ->call('update')
        ->assertHasErrors([
            'form.unitCost'      => 'numeric',
            'form.salePrice'     => 'min',
            'form.stockQuantity' => 'min',
        ]);
});
