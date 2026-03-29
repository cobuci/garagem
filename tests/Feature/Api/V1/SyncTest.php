<?php

use App\Jobs\SyncModelJob;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\Sync\PullSyncService;
use App\Services\Sync\SyncRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

describe('POST /api/v1/sync', function (): void {
    it('requires authentication', function (): void {
        $this->postJson('/api/v1/sync')
            ->assertUnauthorized();
    });

    it('returns 202 accepted with sync_token, synced_at and models list', function (): void {
        Queue::fake();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertAccepted();

        expect($response->json('success'))->toBeTrue();
        expect($response->json('data.sync_token'))->toBeString()->not->toBeEmpty();
        expect($response->json('data.synced_at'))->toBeString()->not->toBeEmpty();
        expect($response->json('data.models'))->toEqual(array_keys(PullSyncService::SYNCABLE_MODELS));
    });

    it('dispatches one SyncModelJob per syncable model', function (): void {
        Queue::fake();

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertAccepted();

        Queue::assertPushed(SyncModelJob::class, count(PullSyncService::SYNCABLE_MODELS));

        foreach (array_keys(PullSyncService::SYNCABLE_MODELS) as $key) {
            Queue::assertPushed(
                SyncModelJob::class,
                fn (SyncModelJob $job) => $job->modelKey === $key,
            );
        }
    });

    it('passes last_synced_at to every dispatched job', function (): void {
        Queue::fake();

        $lastSyncedAt = now()->subDay()->toIso8601String();

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => $lastSyncedAt])
            ->assertAccepted();

        Queue::assertPushed(
            SyncModelJob::class,
            fn (SyncModelJob $job) => $job->since === $lastSyncedAt,
        );
    });

    it('stores all models as pending in the sync registry', function (): void {
        Queue::fake();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertAccepted();

        $syncToken = $response->json('data.sync_token');
        $registry = app(SyncRegistry::class);
        $session = $registry->getSession($syncToken, array_keys(PullSyncService::SYNCABLE_MODELS));

        foreach (array_keys(PullSyncService::SYNCABLE_MODELS) as $key) {
            expect($session['models'][$key]['status'])->toBe('pending');
        }
    });

    it('rejects invalid last_synced_at date', function (): void {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => 'not-a-date'])
            ->assertUnprocessable();
    });
});

describe('GET /api/v1/sync/{syncToken}', function (): void {
    it('requires authentication', function (): void {
        $this->getJson('/api/v1/sync/fake-token')
            ->assertUnauthorized();
    });

    it('returns 404 for unknown or expired sync token', function (): void {
        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/sync/non-existent-token')
            ->assertNotFound();
    });

    it('returns pending status while jobs are still running', function (): void {
        Queue::fake();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertAccepted();

        $syncToken = $response->json('data.sync_token');

        $status = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/sync/{$syncToken}")
            ->assertOk();

        expect($status->json('data.status'))->toBe('pending');
    });

    it('returns completed status and pull payload after all jobs finish', function (): void {
        Queue::fake();

        $categories = Category::factory()->count(2)->create();
        Product::factory()->count(3)->create(['category_id' => $categories->first()->id]);
        Customer::factory()->count(2)->create();

        $totalCategories = Category::count();
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertAccepted();

        $syncToken = $response->json('data.sync_token');

        // Simulate jobs completing by running them via the container (resolves all dependencies).
        Queue::assertPushed(SyncModelJob::class, function (SyncModelJob $job): bool {
            app()->call([$job, 'handle']);

            return true;
        });

        $status = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/sync/{$syncToken}")
            ->assertOk();

        expect($status->json('data.status'))->toBe('completed');
        expect($status->json('data.pull.categories.upsert'))->toHaveCount($totalCategories);
        expect($status->json('data.pull.products.upsert'))->toHaveCount($totalProducts);
        expect($status->json('data.pull.customers.upsert'))->toHaveCount($totalCustomers);
        expect($status->json('data.pull'))->toHaveKeys(array_keys(PullSyncService::SYNCABLE_MODELS));
    });

    it('returns failed status when a job fails', function (): void {
        Queue::fake();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertAccepted();

        $syncToken = $response->json('data.sync_token');
        $registry = app(SyncRegistry::class);

        // Simulate one job completing and one failing.
        $dispatched = Queue::pushed(SyncModelJob::class);
        app()->call([$dispatched[0], 'handle']);
        $dispatched[1]->failed(new RuntimeException('Simulated failure'));

        $status = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/sync/{$syncToken}")
            ->assertOk();

        expect($status->json('data.status'))->toBe('failed');
    });

    it('only returns syncable fields in product payload', function (): void {
        Queue::fake();

        Product::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertAccepted();

        $syncToken = $response->json('data.sync_token');

        Queue::assertPushed(SyncModelJob::class, function (SyncModelJob $job): bool {
            app()->call([$job, 'handle']);

            return true;
        });

        $status = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/sync/{$syncToken}")
            ->assertOk();

        $product = $status->json('data.pull.products.upsert.0');

        expect($product)->toHaveKeys(['id', 'name', 'sale_price', 'stock_quantity']);
        expect($product)->not->toHaveKey('unit_cost');
    });

    it('respects last_synced_at delta in job payload', function (): void {
        Queue::fake();

        $oldCategory = Category::factory()->create(['created_at' => now()->subDays(2), 'updated_at' => now()->subDays(2)]);
        $newCategory = Category::factory()->create(['created_at' => now(), 'updated_at' => now()]);

        $lastSyncedAt = now()->subDay()->toIso8601String();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => $lastSyncedAt])
            ->assertAccepted();

        $syncToken = $response->json('data.sync_token');

        Queue::assertPushed(SyncModelJob::class, function (SyncModelJob $job): bool {
            app()->call([$job, 'handle']);

            return true;
        });

        $status = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/sync/{$syncToken}")
            ->assertOk();

        $upsertIds = collect($status->json('data.pull.categories.upsert'))->pluck('id')->all();

        expect($upsertIds)->toContain($newCategory->id);
        expect($upsertIds)->not->toContain($oldCategory->id);
    });

    it('returns deleted ids in deleted array since last_synced_at', function (): void {
        Queue::fake();

        $category = Category::factory()->create();
        $lastSyncedAt = now()->subMinute()->toIso8601String();

        $category->delete();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync', ['last_synced_at' => $lastSyncedAt])
            ->assertAccepted();

        $syncToken = $response->json('data.sync_token');

        Queue::assertPushed(SyncModelJob::class, function (SyncModelJob $job): bool {
            app()->call([$job, 'handle']);

            return true;
        });

        $status = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/sync/{$syncToken}")
            ->assertOk();

        expect($status->json('data.pull.categories.deleted'))->toContain($category->id);
    });

    it('returns sale_items alongside sales', function (): void {
        Queue::fake();

        Sale::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sync')
            ->assertAccepted();

        $syncToken = $response->json('data.sync_token');

        Queue::assertPushed(SyncModelJob::class, function (SyncModelJob $job): bool {
            app()->call([$job, 'handle']);

            return true;
        });

        $status = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/sync/{$syncToken}")
            ->assertOk();

        expect($status->json('data.pull.sales.upsert'))->not->toBeEmpty();
        expect($status->json('data.pull.sale_items.upsert'))->not->toBeEmpty();
    });
});
