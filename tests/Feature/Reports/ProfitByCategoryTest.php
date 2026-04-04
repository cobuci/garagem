<?php

namespace Tests\Feature\Reports;

use App\Enums\SaleStatus;
use App\Livewire\Reports\ProfitByCategory;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
    DB::table('sale_items')->delete();
    DB::table('sales')->delete();
    DB::table('products')->delete();
    DB::table('categories')->delete();
});

test('it denies access to unauthorized users', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ProfitByCategory::class)
        ->assertForbidden();
});

test('it renders for authorized users', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Livewire::actingAs($user)
        ->test(ProfitByCategory::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.reports.profit-by-category');
});

test('it calculates revenue and profit correctly by category', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('sale_items')->truncate();
    DB::table('sales')->truncate();
    DB::table('products')->truncate();
    DB::table('categories')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $user = User::factory()->create();
    $user->assignRole('admin');

    $category1 = Category::factory()->create(['name' => 'Bebidas']);
    $category2 = Category::factory()->create(['name' => 'Alimentos']);

    $product1 = Product::factory()->create(['category_id' => $category1->id]);
    $product2 = Product::factory()->create(['category_id' => $category2->id]);

    $sale = Sale::factory()->create(['status' => SaleStatus::Paid, 'total_amount' => 15000]);

    // Bebidas: subtotal 10000, cost 6000 (4000 profit)
    $item1 = SaleItem::factory()->make([
        'sale_id'    => $sale->id,
        'product_id' => $product1->id,
        'quantity'   => 2,
        'unit_cost'  => 3000,
        'subtotal'   => 10000,
    ]);
    DB::table('sale_items')->insert($item1->getAttributes());

    // Alimentos: subtotal 5000, cost 2000 (3000 profit)
    $item2 = SaleItem::factory()->make([
        'sale_id'    => $sale->id,
        'product_id' => $product2->id,
        'quantity'   => 1,
        'unit_cost'  => 2000,
        'subtotal'   => 5000,
    ]);
    DB::table('sale_items')->insert($item2->getAttributes());

    Livewire::actingAs($user)
        ->test(ProfitByCategory::class)
        ->assertSet('chartDataArray.labels', ['Bebidas', 'Alimentos'])
        ->assertSet('chartDataArray.revenue', [100.0, 50.0])
        ->assertSet('chartDataArray.profit', [40.0, 30.0]);
});

test('it filters by period', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('sale_items')->truncate();
    DB::table('sales')->truncate();
    DB::table('products')->truncate();
    DB::table('categories')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $user = User::factory()->create();
    $user->assignRole('admin');

    $category = Category::factory()->create(['name' => 'Geral']);
    $product = Product::factory()->create(['category_id' => $category->id]);

    // Old sale (90 days ago)
    $oldSale = Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'created_at' => Carbon::now()->subDays(90),
    ]);
    $oldItem = SaleItem::factory()->make([
        'sale_id'    => $oldSale->id,
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_cost'  => 1000,
        'subtotal'   => 2000,
    ]);
    DB::table('sale_items')->insert($oldItem->getAttributes());

    // Recent sale (today)
    $recentSale = Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'created_at' => Carbon::now(),
    ]);
    $recentItem = SaleItem::factory()->make([
        'sale_id'    => $recentSale->id,
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_cost'  => 1000,
        'subtotal'   => 2000,
    ]);
    DB::table('sale_items')->insert($recentItem->getAttributes());

    // Default (last 30 days) should only have recent sale
    Livewire::actingAs($user)
        ->test(ProfitByCategory::class)
        ->assertSet('chartDataArray.revenue', [20.0])
        // Change to last 12 months (last_year in code)
        ->set('period', 'last_year')
        ->assertSet('chartDataArray.revenue', [40.0]);
});

test('it ignores cancelled sales', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('sale_items')->truncate();
    DB::table('sales')->truncate();
    DB::table('products')->truncate();
    DB::table('categories')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $user = User::factory()->create();
    $user->assignRole('admin');

    $category = Category::factory()->create(['name' => 'Geral']);
    $product = Product::factory()->create(['category_id' => $category->id]);

    $cancelledSale = Sale::factory()->create(['status' => SaleStatus::Cancelled]);
    $cancelledItem = SaleItem::factory()->make([
        'sale_id'    => $cancelledSale->id,
        'product_id' => $product->id,
        'quantity'   => 1,
        'subtotal'   => 2000,
    ]);
    DB::table('sale_items')->insert($cancelledItem->getAttributes());

    Livewire::actingAs($user)
        ->test(ProfitByCategory::class)
        ->assertSet('chartDataArray.labels', []);
});
