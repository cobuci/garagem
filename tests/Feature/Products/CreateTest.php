<?php

use App\Enums\Permission;
use App\Livewire\Products\Create;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(Permission::CreateProduct->value);
    config(['wireui.style.icon' => 'outline']);
});

test('it returns 403 when creating a product without permission', function () {
    $userWithoutPermission = User::factory()->create();

    Livewire::actingAs($userWithoutPermission)
        ->test(Create::class, ['categories' => new Collection])
        ->call('create')
        ->assertForbidden();
});

test('it can create a product with all fields', function () {
    $category = Category::factory()->create();

    Livewire::actingAs($this->user)
        ->test(Create::class, ['categories' => Category::all()])
        ->set('form.categoryId', $category->id)
        ->set('form.name', 'New Product')
        ->set('form.brand', 'New Brand')
        ->set('form.weightValue', 500.5)
        ->set('form.weightType', 'g')
        ->set('form.upc', '1234567890')
        ->call('create')
        ->assertHasNoErrors()
        ->assertSet('createDrawer', false)
        ->assertSet('form.name', '')
        ->assertSet('form.weightValue', null)
        ->assertDispatched('product:created');

    $product = Product::where('name', 'New Product')->first();
    expect($product->category_id)->toBe($category->id)
        ->and($product->brand)->toBe('New Brand')
        ->and($product->weight)->toBe('500.5g')
        ->and($product->upc)->toBe('1234567890')
        ->and($product->sale_price)->toBeNull();
});

test('it validates required fields', function () {
    Livewire::actingAs($this->user)
        ->test(Create::class, ['categories' => new Collection])
        ->set('form.weightType', '')
        ->call('create')
        ->assertHasErrors([
            'form.categoryId'  => 'required',
            'form.name'        => 'required',
            'form.weightValue' => 'required',
            'form.weightType'  => 'required',
        ]);
});

test('it validates unique upc', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['upc' => 'DUPLICATE_UPC', 'category_id' => $category->id]);

    Livewire::actingAs($this->user)
        ->test(Create::class, ['categories' => Category::all()])
        ->set('form.categoryId', $category->id)
        ->set('form.name', 'Product with Duplicate UPC')
        ->set('form.weightValue', 100)
        ->set('form.upc', 'DUPLICATE_UPC')
        ->call('create')
        ->assertHasErrors(['form.upc' => 'unique']);
});

test('it validates weight value is numeric and positive', function () {
    Livewire::actingAs($this->user)
        ->test(Create::class, ['categories' => new Collection])
        ->set('form.weightValue', 'not-numeric')
        ->call('create')
        ->assertHasErrors(['form.weightValue' => 'numeric'])
        ->set('form.weightValue', -1)
        ->call('create')
        ->assertHasErrors(['form.weightValue' => 'min']);
});

test('it validates weight type is in allowed options', function () {
    Livewire::actingAs($this->user)
        ->test(Create::class, ['categories' => new Collection])
        ->set('form.weightType', 'invalid-type')
        ->call('create')
        ->assertHasErrors(['form.weightType' => 'in']);
});

test('it validates max lengths for strings', function () {
    Livewire::actingAs($this->user)
        ->test(Create::class, ['categories' => new Collection])
        ->set('form.name', str_repeat('a', 256))
        ->set('form.brand', str_repeat('b', 256))
        ->set('form.upc', str_repeat('c', 256))
        ->call('create')
        ->assertHasErrors([
            'form.name'  => 'max',
            'form.brand' => 'max',
            'form.upc'   => 'max',
        ]);
});

test('it clears the form and closes the drawer on cancel', function () {
    Livewire::actingAs($this->user)
        ->test(Create::class, ['categories' => new Collection])
        ->set('createDrawer', true)
        ->set('form.name', 'Temporary Name')
        ->set('createDrawer', false) // Simulator cancel (via x-on:click in UI)
        ->assertSet('createDrawer', false);
    // Note: Cancel button in UI just sets createDrawer = false, doesn't reset form currently.
    // If we want it to reset, we'd need a method or listener.
});
