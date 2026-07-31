<?php

use App\Enums\Permission;
use App\Livewire\Categories\Index;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->givePermissionTo([
        Permission::ViewCategory->value,
        Permission::CreateCategory->value,
        Permission::EditCategory->value,
        Permission::DeleteCategory->value,
    ]);
    actingAs($this->user);
    config(['wireui.style.icon' => 'outline']);
});

it('can render the categories page', function () {
    Livewire::test(Index::class)
        ->assertSuccessful()
        ->assertSee(__('categories.title'));
});

it('can list categories', function () {
    Category::factory()->create(['name' => 'Bebidas']);
    Category::factory()->create(['name' => 'Doces']);

    Livewire::test(Index::class)
        ->assertSee('Bebidas')
        ->assertSee('Doces');
});

it('can create a category with automatic sort order', function () {
    Category::factory()->create(['name' => 'Existente', 'sort_order' => 3]);

    Livewire::test(Index::class)
        ->call('create')
        ->set('form.name', 'Bebidas')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showDrawer', false);

    assertDatabaseHas('categories', [
        'name'       => 'Bebidas',
        'sort_order' => 4,
    ]);
});

it('can edit a category', function () {
    $category = Category::factory()->create([
        'name'       => 'Antiga',
        'sort_order' => 1,
    ]);

    Livewire::test(Index::class)
        ->call('edit', $category->id)
        ->assertSet('form.name', 'Antiga')
        ->set('form.name', 'Atualizada')
        ->call('save')
        ->assertHasNoErrors();

    expect($category->fresh())
        ->name->toBe('Atualizada')
        ->sort_order->toBe(1);
});

it('can reorder categories', function () {
    $first = Category::factory()->create(['name' => 'Primeira', 'sort_order' => 1]);
    $second = Category::factory()->create(['name' => 'Segunda', 'sort_order' => 2]);
    $third = Category::factory()->create(['name' => 'Terceira', 'sort_order' => 3]);

    Livewire::test(Index::class)
        ->call('sort', $third->id, 0)
        ->assertHasNoErrors();

    expect($third->fresh()->sort_order)->toBe(1)
        ->and($first->fresh()->sort_order)->toBe(2)
        ->and($second->fresh()->sort_order)->toBe(3);
});

it('validates required fields when creating', function () {
    Livewire::test(Index::class)
        ->call('create')
        ->call('save')
        ->assertHasErrors([
            'form.name' => 'required',
        ]);
});

it('validates unique category name', function () {
    Category::factory()->create(['name' => 'Bebidas']);

    Livewire::test(Index::class)
        ->call('create')
        ->set('form.name', 'Bebidas')
        ->call('save')
        ->assertHasErrors(['form.name' => 'unique']);
});

it('allows reusing a soft deleted category name', function () {
    $category = Category::factory()->create(['name' => 'Bebidas']);
    $category->delete();

    Livewire::test(Index::class)
        ->call('create')
        ->set('form.name', 'Bebidas')
        ->call('save')
        ->assertHasNoErrors();

    expect(Category::where('name', 'Bebidas')->exists())->toBeTrue();
});

it('denies access to unauthorized user', function () {
    $unauthorized = User::factory()->create();
    actingAs($unauthorized);

    Livewire::test(Index::class)
        ->assertForbidden();
});

it('denies create without permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::ViewCategory->value);
    actingAs($user);

    Livewire::test(Index::class)
        ->call('create')
        ->assertForbidden();
});

it('denies reorder without permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::ViewCategory->value);
    actingAs($user);

    $category = Category::factory()->create(['sort_order' => 1]);

    Livewire::test(Index::class)
        ->call('sort', $category->id, 0)
        ->assertForbidden();
});
