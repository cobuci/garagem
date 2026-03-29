<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

describe('POST /api/v1/sync', function (): void {
    it('requires authentication', function (): void {
        $this->postJson('/api/v1/sync')
            ->assertUnauthorized();
    });

    it('returns sync structure on first sync (no last_synced_at)', function (): void {
        Category::factory()->count(2)->create();
        Product::factory()->count(3)->create();
        Customer::factory()->count(2)->create();

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'synced_at',
                    'pull' => [
                        'products'   => ['upsert', 'deleted'],
                        'categories' => ['upsert', 'deleted'],
                        'customers'  => ['upsert', 'deleted'],
                        'sales'      => ['upsert', 'deleted'],
                        'sale_items' => ['upsert', 'deleted'],
                    ],
                ],
            ])
            ->assertJsonPath('success', true);
    });

    it('returns all records on first sync', function (): void {
        $categories = Category::factory()->count(2)->create();
        Product::factory()->count(3)->create(['category_id' => $categories->first()->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        expect($response->json('data.pull.categories.upsert'))->toHaveCount(2);
        expect($response->json('data.pull.products.upsert'))->toHaveCount(3);
        expect($response->json('data.pull.categories.deleted'))->toBeEmpty();
        expect($response->json('data.pull.products.deleted'))->toBeEmpty();
    });

    it('returns only records modified since last_synced_at', function (): void {
        $oldCategory = Category::factory()->create(['created_at' => now()->subDays(2), 'updated_at' => now()->subDays(2)]);
        $newCategory = Category::factory()->create(['created_at' => now(), 'updated_at' => now()]);

        $lastSyncedAt = now()->subDay()->toIso8601String();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => $lastSyncedAt])
            ->assertOk();

        $upsertIds = collect($response->json('data.pull.categories.upsert'))->pluck('id')->all();

        expect($upsertIds)->toContain($newCategory->id);
        expect($upsertIds)->not->toContain($oldCategory->id);
    });

    it('returns deleted ids in deleted array since last_synced_at', function (): void {
        $category = Category::factory()->create();
        $lastSyncedAt = now()->subMinute()->toIso8601String();

        $category->delete();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => $lastSyncedAt])
            ->assertOk();

        expect($response->json('data.pull.categories.deleted'))->toContain($category->id);
    });

    it('does not return deleted ids in upsert', function (): void {
        $category = Category::factory()->create();
        $category->delete();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        $upsertIds = collect($response->json('data.pull.categories.upsert'))->pluck('id')->all();

        expect($upsertIds)->not->toContain($category->id);
    });

    it('returns empty deleted array on first sync even if records were deleted before', function (): void {
        $category = Category::factory()->create();
        $category->delete();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        expect($response->json('data.pull.categories.deleted'))->toBeEmpty();
    });

    it('returns synced_at timestamp from server', function (): void {
        $before = now()->subSecond();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        $syncedAt = Carbon::parse($response->json('data.synced_at'));

        expect($syncedAt->isAfter($before))->toBeTrue();
    });

    it('rejects invalid last_synced_at date', function (): void {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => 'not-a-date'])
            ->assertUnprocessable();
    });

    it('only returns syncable fields in product upsert payload', function (): void {
        Product::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        $product = $response->json('data.pull.products.upsert.0');

        expect($product)->toHaveKeys(['id', 'name', 'sale_price', 'stock_quantity']);
        expect($product)->not->toHaveKey('unit_cost');
    });

    it('returns sale_items alongside sales', function (): void {
        Sale::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        expect($response->json('data.pull.sales.upsert'))->not->toBeEmpty();
        expect($response->json('data.pull.sale_items.upsert'))->not->toBeEmpty();
    });
});
