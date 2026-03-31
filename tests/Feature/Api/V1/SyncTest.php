<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Services\Sync\PullSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

describe('POST /api/v1/sync', function (): void {
    it('requires authentication', function (): void {
        $this->postJson('/api/v1/sync')
            ->assertUnauthorized();
    });

    it('returns 200 with synced_at and pull payload', function (): void {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        expect($response->json('success'))->toBeTrue()
            ->and($response->json('data.synced_at'))->toBeString()->not->toBeEmpty()
            ->and($response->json('data.pull'))->toHaveKeys(array_keys(PullSyncService::SYNCABLE_MODELS));
    });

    it('returns full payload on first sync with no last_synced_at', function (): void {
        $categories = Category::factory()->count(2)->create();
        Product::factory()->count(3)->create(['category_id' => $categories->first()->id]);
        Customer::factory()->count(2)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        expect($response->json('data.pull.categories.upsert'))->toHaveCount(Category::count())
            ->and($response->json('data.pull.products.upsert'))->toHaveCount(Product::count())
            ->and($response->json('data.pull.customers.upsert'))->toHaveCount(Customer::count());
    });

    it('returns only delta records when last_synced_at is provided', function (): void {
        $oldCategory = Category::factory()->create(['created_at' => now()->subDays(2), 'updated_at' => now()->subDays(2)]);
        $newCategory = Category::factory()->create(['created_at' => now(), 'updated_at' => now()]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => now()->subDay()->toIso8601String()])
            ->assertOk();

        $upsertIds = collect($response->json('data.pull.categories.upsert'))->pluck('id')->all();

        expect($upsertIds)->toContain($newCategory->id)
            ->and($upsertIds)->not->toContain($oldCategory->id);
    });

    it('returns deleted ids since last_synced_at', function (): void {
        $category = Category::factory()->create();
        $category->delete();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => now()->subMinute()->toIso8601String()])
            ->assertOk();

        expect($response->json('data.pull.categories.deleted'))->toContain($category->id);
    });

    it('does not include unit_cost in product payload', function (): void {
        Product::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        $product = $response->json('data.pull.products.upsert.0');

        expect($product)->toHaveKeys(['id', 'name', 'sale_price', 'stock_quantity'])
            ->and($product)->not->toHaveKey('unit_cost');
    });

    it('does not include sales or sale_items in payload', function (): void {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        expect($response->json('data.pull'))->not->toHaveKey('sales')
            ->and($response->json('data.pull'))->not->toHaveKey('sale_items');
    });

    it('rejects an invalid last_synced_at value', function (): void {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => 'not-a-date'])
            ->assertUnprocessable();
    });
});
