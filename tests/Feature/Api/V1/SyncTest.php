<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Services\Sync\PullSyncService;
use App\Traits\HasMobileSync;
use Illuminate\Database\Eloquent\Model;
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

    it('returns 200 with synced_at, has_more, cursors and pull payload', function (): void {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        expect($response->json('success'))->toBeTrue()
            ->and($response->json('data.synced_at'))->toBeString()->not->toBeEmpty()
            ->and($response->json('data.has_more'))->toBeFalse()
            ->and($response->json('data.cursors'))->toBeNull()
            ->and($response->json('data.pull'))->toHaveKeys(array_keys(PullSyncService::SYNCABLE_MODELS));
    });

    it('paginates full sync when exceeding PAGE_SIZE', function (): void {
        $pageSize = PullSyncService::PAGE_SIZE;
        Product::factory()->count($pageSize + 10)->create();

        Category::factory()->count(2)->create();
        Customer::factory()->count(2)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        expect($response->json('data.has_more'))->toBeTrue()
            ->and($response->json('data.cursors.products'))->toBeInt()
            ->and($response->json('data.pull.products.upsert'))->toHaveCount($pageSize);

        $cursor = $response->json('data.cursors.products');

        $secondResponse = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['cursors' => ['products' => $cursor]])
            ->assertOk();

        expect($secondResponse->json('data.has_more'))->toBeFalse()
            ->and($secondResponse->json('data.cursors'))->toBeNull()
            ->and($secondResponse->json('data.pull.products.upsert'))->toHaveCount(10)
            ->and($secondResponse->json('data.pull'))->not->toHaveKey('categories')
            ->and($secondResponse->json('data.pull'))->not->toHaveKey('customers');
    });

    it('does not paginate delta sync even if exceeding PAGE_SIZE', function (): void {
        $pageSize = PullSyncService::PAGE_SIZE;
        Product::factory()->count($pageSize + 10)->create(['updated_at' => now()]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => now()->subDay()->toIso8601String()])
            ->assertOk();

        expect($response->json('data.has_more'))->toBeFalse()
            ->and($response->json('data.pull.products.upsert'))->toHaveCount($pageSize + 10);
    });

    it('throws LogicException when getSyncableFields is empty', function (): void {
        $model = new class extends Model
        {
            use HasMobileSync;

            public function getSyncableFields(): array
            {
                return [];
            }
        };

        $model->toSyncArray();
    })->throws(LogicException::class);

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

    it('does not include created_at in category payload', function (): void {
        Category::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        $category = $response->json('data.pull.categories.upsert.0');

        expect($category)->toHaveKeys(['id', 'name', 'updated_at'])
            ->and($category)->not->toHaveKey('created_at');
    });

    it('omits models with no changes in delta sync', function (): void {
        Category::factory()->create(['updated_at' => now()->subDays(2), 'created_at' => now()->subDays(2)]);
        Customer::factory()->create(['updated_at' => now()]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => now()->subDay()->toIso8601String()])
            ->assertOk();

        expect($response->json('data.pull'))->toHaveKey('customers')
            ->and($response->json('data.pull'))->not->toHaveKey('categories')
            ->and($response->json('data.pull'))->not->toHaveKey('products');
    });

    it('always returns all models on full sync even if empty', function (): void {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        expect($response->json('data.pull'))->toHaveKeys(['categories', 'products', 'customers']);
    });

    it('returns synced_at timestamp captured after the sync', function (): void {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertOk();

        $syncedAt = Carbon::parse($response->json('data.synced_at'));
        expect($syncedAt->isAfter(now()->subSeconds(5)))->toBeTrue();
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
