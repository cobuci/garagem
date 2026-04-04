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
use Livewire\Livewire;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
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

test('it includes categories from sales in the selected period', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $category = Category::factory()->create(['name' => 'Bebidas Especiais XYZ']);
    $product = Product::factory()->create(['category_id' => $category->id]);
    $sale = Sale::factory()->create(['status' => SaleStatus::Paid]);
    SaleItem::factory()->create(['sale_id' => $sale->id, 'product_id' => $product->id]);

    Livewire::actingAs($user)
        ->test(ProfitByCategory::class)
        ->assertSet('chartDataArray.labels', fn ($labels) => in_array('Bebidas Especiais XYZ', $labels));
});

test('it excludes categories from sales outside the period', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $category = Category::factory()->create(['name' => 'Categoria Antiga XYZ']);
    $product = Product::factory()->create(['category_id' => $category->id]);

    // Sale 90 days ago — outside last_30_days window
    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'created_at' => Carbon::now()->subDays(90),
    ]);
    SaleItem::factory()->create(['sale_id' => $sale->id, 'product_id' => $product->id]);

    Livewire::actingAs($user)
        ->test(ProfitByCategory::class)
        ->assertSet('chartDataArray.labels', fn ($labels) => ! in_array('Categoria Antiga XYZ', $labels));
});

test('it shows category when period is extended to cover old sales', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $category = Category::factory()->create(['name' => 'Categoria Antiga XYZ2']);
    $product = Product::factory()->create(['category_id' => $category->id]);

    // Sale 60 days ago — outside last_30_days but inside last_6_months
    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'created_at' => Carbon::now()->subDays(60),
    ]);
    SaleItem::factory()->create(['sale_id' => $sale->id, 'product_id' => $product->id]);

    Livewire::actingAs($user)
        ->test(ProfitByCategory::class)
        ->assertSet('chartDataArray.labels', fn ($labels) => ! in_array('Categoria Antiga XYZ2', $labels))
        ->set('period', 'last_6_months')
        ->assertSet('chartDataArray.labels', fn ($labels) => in_array('Categoria Antiga XYZ2', $labels));
});

test('it ignores cancelled sales', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $category = Category::factory()->create(['name' => 'Categoria Cancelada XYZ']);
    $product = Product::factory()->create(['category_id' => $category->id]);

    $sale = Sale::factory()->create(['status' => SaleStatus::Cancelled]);
    SaleItem::factory()->create(['sale_id' => $sale->id, 'product_id' => $product->id]);

    Livewire::actingAs($user)
        ->test(ProfitByCategory::class)
        ->assertSet('chartDataArray.labels', fn ($labels) => ! in_array('Categoria Cancelada XYZ', $labels));
});
