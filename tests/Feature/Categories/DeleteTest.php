<?php

use App\Enums\Permission;
use App\Livewire\Categories\Delete;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(Permission::DeleteCategory->value);
    config(['wireui.style.icon' => 'outline']);
});

test('it returns 403 when deleting a category without permission', function () {
    $userWithoutPermission = User::factory()->create();
    $category = Category::factory()->create();

    Livewire::actingAs($userWithoutPermission)
        ->test(Delete::class)
        ->dispatch('category:delete', category: $category->id)
        ->assertForbidden();

    Livewire::actingAs($userWithoutPermission)
        ->test(Delete::class)
        ->call('destroy')
        ->assertForbidden();
});

test('it can soft delete a category with correct confirmation', function () {
    $category = Category::factory()->create(['name' => 'Vazia']);

    Livewire::actingAs($this->user)
        ->test(Delete::class)
        ->dispatch('category:delete', category: $category->id)
        ->assertSet('category.id', $category->id)
        ->assertSet('deleteModal', true)
        ->set('confirmation', __('categories.delete_word'))
        ->call('destroy')
        ->assertSet('deleteModal', false)
        ->assertDispatched('category:deleted');

    assertSoftDeleted('categories', ['id' => $category->id]);
});

test('it cannot delete a category with incorrect confirmation', function () {
    $category = Category::factory()->create();

    Livewire::actingAs($this->user)
        ->test(Delete::class)
        ->dispatch('category:delete', category: $category->id)
        ->set('confirmation', 'palavra-errada')
        ->call('destroy')
        ->assertSet('deleteModal', true)
        ->assertNotDispatched('category:deleted');

    assertDatabaseHas('categories', [
        'id'         => $category->id,
        'deleted_at' => null,
    ]);
});

test('it cannot open delete modal for a category with products', function () {
    $category = Category::factory()->create(['name' => 'Com Produtos']);
    Product::factory()->create(['category_id' => $category->id]);

    Livewire::actingAs($this->user)
        ->test(Delete::class)
        ->dispatch('category:delete', category: $category->id)
        ->assertSet('deleteModal', false)
        ->assertSet('category', null);

    assertDatabaseHas('categories', [
        'id'         => $category->id,
        'deleted_at' => null,
    ]);
});

test('it resets confirmation when a new category is selected for deletion', function () {
    $category = Category::factory()->create();

    Livewire::actingAs($this->user)
        ->test(Delete::class)
        ->set('confirmation', 'algum-texto')
        ->dispatch('category:delete', category: $category->id)
        ->assertSet('confirmation', '');
});
