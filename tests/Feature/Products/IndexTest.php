<?php

use App\Livewire\Products\Index;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    config(['wireui.style.icon' => 'outline']);
});

test('it redirects guests to login', function () {
    get(route('products.index'))
        ->assertRedirect(route('login'));
});

test('it renders the products index page for authenticated users', function () {
    actingAs($this->user)
        ->get(route('products.index'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

test('it defaults to latest products tab (ID 0) on mount', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSet('selectedCategoryId', 0);
});

test('it displays the latest 10 products on the default tab', function () {
    $category = Category::factory()->create(['icon' => 'tag']);
    Product::factory(5)->create(['created_at' => now()->subDays(10), 'category_id' => $category->id]);
    $latestProducts = Product::factory(10)->create(['created_at' => now(), 'category_id' => $category->id]);
    Product::factory(5)->create(['created_at' => now()->subDays(20), 'category_id' => $category->id]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertCount('products', 10)
        ->tap(fn ($component) => expect($component->get('products')->pluck('id')->diff($latestProducts->pluck('id')))->toBeEmpty());
});

test('it lists categories ordered by sort_order and name', function () {
    Category::factory()->create(['name' => 'B', 'sort_order' => 2, 'icon' => 'tag']);
    Category::factory()->create(['name' => 'A', 'sort_order' => 1, 'icon' => 'tag']);
    Category::factory()->create(['name' => 'C', 'sort_order' => 1, 'icon' => 'tag']);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->tap(fn ($component) => expect($component->get('categories')->pluck('name')->toArray())->toBe(['A', 'C', 'B']));
});

test('it filters products by selected category', function () {
    $categoryA = Category::factory()->create(['icon' => 'tag']);
    $categoryB = Category::factory()->create(['icon' => 'tag']);

    $productA = Product::factory()->create(['category_id' => $categoryA->id, 'name' => 'Product A']);
    Product::factory()->create(['category_id' => $categoryB->id, 'name' => 'Product B']);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('selectCategory', $categoryA->id)
        ->assertSet('selectedCategoryId', $categoryA->id)
        ->tap(function ($component) use ($productA) {
            $products = $component->get('products');
            expect($products->total())->toBe(1)
                ->and($products->first()->id)->toBe($productA->id);
        });
});

test('it resets pagination when switching categories', function () {
    $category = Category::factory()->create(['icon' => 'tag']);
    Product::factory(25)->create(['category_id' => $category->id]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('selectCategory', $category->id)
        ->set('paginators.page', 2)
        ->call('selectCategory', 0)
        ->assertSet('paginators.page', 1);
});

test('it calculates correct stats for all products', function () {
    Product::factory()->create([
        'unit_cost'      => 5,
        'sale_price'     => 10,
        'stock_quantity' => 10,
        'category_id'    => Category::factory()->create(['icon' => 'tag'])->id,
    ]);

    Product::factory()->create([
        'unit_cost'      => 20,
        'sale_price'     => 30,
        'stock_quantity' => 5,
        'category_id'    => Category::factory()->create(['icon' => 'tag'])->id,
    ]);

    Product::factory()->create([
        'unit_cost'      => 100,
        'sale_price'     => 200,
        'stock_quantity' => 0,
        'category_id'    => Category::factory()->create(['icon' => 'tag'])->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->tap(function ($component) {
            $stats = $component->get('stats');
            expect($stats['total_cost'])->toEqual(150.0)
                ->and($stats['total_sale'])->toEqual(250.0)
                ->and($stats['total_profit'])->toEqual(100.0);
        });
});

test('it orders products by stock availability then by name', function () {
    $category = Category::factory()->create(['icon' => 'tag']);

    $productInStockB = Product::factory()->create([
        'category_id'    => $category->id,
        'name'           => 'B - In Stock',
        'stock_quantity' => 10,
    ]);

    $productOutOfStock = Product::factory()->create([
        'category_id'    => $category->id,
        'name'           => 'A - Out of Stock',
        'stock_quantity' => 0,
    ]);

    $productInStockA = Product::factory()->create([
        'category_id'    => $category->id,
        'name'           => 'A - In Stock',
        'stock_quantity' => 5,
    ]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('selectCategory', $category->id)
        ->tap(function ($component) use ($productInStockA, $productInStockB, $productOutOfStock) {
            $ids = $component->get('products')->pluck('id')->toArray();
            expect($ids)->toBe([
                $productInStockA->id,
                $productInStockB->id,
                $productOutOfStock->id,
            ]);
        });
});

test('it shows empty message when no products exist in category', function () {
    $category = Category::factory()->create(['icon' => 'tag']);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('selectCategory', $category->id)
        ->assertSee(__('products.empty'));
});
