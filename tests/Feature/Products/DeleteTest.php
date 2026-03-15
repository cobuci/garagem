<?php

namespace Tests\Feature\Products;

use App\Livewire\Products\Delete;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    config(['wireui.style.icon' => 'outline']);
});

test('it can delete a product with correct confirmation', function () {
    $category = Category::factory()->create(['icon' => 'tag']);
    $product = Product::factory()->create(['category_id' => $category->id]);

    Livewire::actingAs($this->user)
        ->test(Delete::class)
        ->dispatch('product:delete', product: $product->id)
        ->assertSet('product.id', $product->id)
        ->assertSet('deleteModal', true)
        ->set('confirmation', 'delete')
        ->call('destroy')
        ->assertSet('deleteModal', false)
        ->assertDispatched('product:updated');

    assertSoftDeleted('products', ['id' => $product->id]);
});

test('it cannot delete a product with incorrect confirmation', function () {
    $category = Category::factory()->create(['icon' => 'tag']);
    $product = Product::factory()->create(['category_id' => $category->id]);

    Livewire::actingAs($this->user)
        ->test(Delete::class)
        ->dispatch('product:delete', product: $product->id)
        ->set('confirmation', 'wrong-word')
        ->call('destroy')
        ->assertSet('deleteModal', true)
        ->assertNotDispatched('product:deleted');

    assertDatabaseHas('products', ['id' => $product->id]);
});

test('it resets confirmation when a new product is selected for deletion', function () {
    $category = Category::factory()->create(['icon' => 'tag']);
    $product = Product::factory()->create(['category_id' => $category->id]);

    Livewire::actingAs($this->user)
        ->test(Delete::class)
        ->set('confirmation', 'some-text')
        ->dispatch('product:delete', product: $product->id)
        ->assertSet('confirmation', '');
});
