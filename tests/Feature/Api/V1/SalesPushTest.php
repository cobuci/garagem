<?php

use App\Enums\MobileSaleStatus;
use App\Models\Customer;
use App\Models\MobileSale;
use App\Models\MobileSaleItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

/** @return array<string, mixed> */
function validSalePayload(?int $customerId = null, ?string $customerName = 'John Doe', ?int $productId = null): array
{
    return [
        'local_id'           => (string) Str::uuid(),
        'customer_id'        => $customerId,
        'customer_name'      => $customerName,
        'total_amount_cents' => 3980,
        'created_at'         => 1774789200000,
        'items'              => [
            [
                'product_id'       => $productId ?? Product::factory()->create()->id,
                'unit_price_cents' => 1990,
                'quantity'         => 2,
                'subtotal_cents'   => 3980,
            ],
        ],
    ];
}

describe('POST /api/v1/sales/push', function (): void {
    it('requires authentication', function (): void {
        $this->postJson('/api/v1/sales/push', ['sales' => []])
            ->assertUnauthorized();
    });

    it('creates a single sale with items and returns local_id and server_id', function (): void {
        $product = Product::factory()->create();
        $localId = (string) Str::uuid();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    [
                        'local_id'           => $localId,
                        'customer_id'        => null,
                        'customer_name'      => 'John Doe',
                        'total_amount_cents' => 3980,
                        'created_at'         => 1774789200000,
                        'items'              => [
                            [
                                'product_id'       => $product->id,
                                'unit_price_cents' => 1990,
                                'quantity'         => 2,
                                'subtotal_cents'   => 3980,
                            ],
                        ],
                    ],
                ],
            ])
            ->assertOk();

        $created = $response->json('data.created.0');

        expect($response->json('success'))->toBeTrue()
            ->and($response->json('data.created'))->toHaveCount(1)
            ->and($response->json('data.failed'))->toBeEmpty()
            ->and($created['local_id'])->toBe($localId)
            ->and($created['server_id'])->toBeInt();

        $mobileSale = MobileSale::query()->find($created['server_id']);

        expect($mobileSale)->not->toBeNull()
            ->and($mobileSale->local_id)->toBe($localId)
            ->and($mobileSale->total_amount_cents)->toBe(3980)
            ->and($mobileSale->customer_name)->toBe('John Doe')
            ->and($mobileSale->status)->toBe(MobileSaleStatus::Pending)
            ->and(MobileSaleItem::query()->where('mobile_sale_id', $mobileSale->id)->count())->toBe(1);
    });

    it('creates a batch of sales and returns all in created[]', function (): void {
        $product = Product::factory()->create();

        $sales = collect(range(1, 3))->map(fn () => validSalePayload(productId: $product->id))->all();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', ['sales' => $sales])
            ->assertOk();

        expect($response->json('data.created'))->toHaveCount(3)
            ->and($response->json('data.failed'))->toBeEmpty()
            ->and(MobileSale::query()->count())->toBe(3);
    });

    it('accepts a sale with null customer_id and a free-text customer name', function (): void {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [validSalePayload(customerName: 'Maria Avulsa', productId: $product->id)],
            ])
            ->assertOk();

        $mobileSale = MobileSale::query()->find($response->json('data.created.0.server_id'));

        expect($mobileSale->customer_id)->toBeNull()
            ->and($mobileSale->customer_name)->toBe('Maria Avulsa');
    });

    it('accepts a sale linked to an existing customer', function (): void {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [validSalePayload(customerId: $customer->id, productId: $product->id)],
            ])
            ->assertOk();

        $mobileSale = MobileSale::query()->find($response->json('data.created.0.server_id'));

        expect($mobileSale->customer_id)->toBe($customer->id);
    });

    it('preserves the device created_at timestamp', function (): void {
        $product = Product::factory()->create();

        $payload = validSalePayload(productId: $product->id);
        $payload['created_at'] = 1768465800000;

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', ['sales' => [$payload]])
            ->assertOk();

        $mobileSale = MobileSale::query()->find($response->json('data.created.0.server_id'));

        expect($mobileSale->device_created_at->toDateString())->toBe('2026-01-15');
    });

    it('updates an existing pending sale on re-push', function (): void {
        $product = Product::factory()->create();
        $localId = (string) Str::uuid();

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    array_merge(validSalePayload(productId: $product->id), [
                        'local_id'      => $localId,
                        'customer_name' => 'Original Name',
                    ]),
                ],
            ])
            ->assertOk();

        $newProduct = Product::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    [
                        'local_id'           => $localId,
                        'customer_id'        => null,
                        'customer_name'      => 'Updated Name',
                        'total_amount_cents' => 2000,
                        'created_at'         => 1774789200000,
                        'items'              => [
                            [
                                'product_id'       => $newProduct->id,
                                'unit_price_cents' => 2000,
                                'quantity'         => 1,
                                'subtotal_cents'   => 2000,
                            ],
                        ],
                    ],
                ],
            ])
            ->assertOk();

        expect($response->json('data.created'))->toHaveCount(1)
            ->and($response->json('data.failed'))->toBeEmpty()
            ->and($response->json('data.created.0.local_id'))->toBe($localId)
            ->and(MobileSale::query()->where('local_id', $localId)->count())->toBe(1);

        $mobileSale = MobileSale::query()->where('local_id', $localId)->first();

        expect($mobileSale->customer_name)->toBe('Updated Name')
            ->and($mobileSale->total_amount_cents)->toBe(2000)
            ->and($mobileSale->items()->count())->toBe(1)
            ->and($mobileSale->items()->first()->product_id)->toBe($newProduct->id);
    });

    it('rejects a re-push of a synced sale and reports it in failed[]', function (): void {
        $product = Product::factory()->create();
        $localId = (string) Str::uuid();

        MobileSale::factory()->create(['local_id' => $localId, 'status' => MobileSaleStatus::Synced]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    array_merge(validSalePayload(productId: $product->id), [
                        'local_id'      => $localId,
                        'customer_name' => 'Attempt Update',
                    ]),
                ],
            ])
            ->assertOk();

        expect($response->json('data.created'))->toBeEmpty()
            ->and($response->json('data.failed'))->toHaveCount(1)
            ->and($response->json('data.failed.0.local_id'))->toBe($localId)
            ->and($response->json('data.failed.0.error'))->toBeString()->not->toBeEmpty();
    });

    it('reports a failed sale in failed[] without aborting the rest of the batch', function (): void {
        $product = Product::factory()->create();
        $syncedLocalId = (string) Str::uuid();
        $goodLocalId = (string) Str::uuid();

        MobileSale::factory()->create(['local_id' => $syncedLocalId, 'status' => MobileSaleStatus::Synced]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    array_merge(validSalePayload(productId: $product->id), [
                        'local_id'      => $syncedLocalId,
                        'customer_name' => 'Will Fail',
                    ]),
                    array_merge(validSalePayload(productId: $product->id), [
                        'local_id'      => $goodLocalId,
                        'customer_name' => 'Good',
                    ]),
                ],
            ])
            ->assertOk();

        expect($response->json('data.created'))->toHaveCount(1)
            ->and($response->json('data.failed'))->toHaveCount(1)
            ->and($response->json('data.created.0.local_id'))->toBe($goodLocalId)
            ->and($response->json('data.failed.0.local_id'))->toBe($syncedLocalId)
            ->and($response->json('data.failed.0.error'))->toBeString()->not->toBeEmpty();
    });

    it('returns 422 when sales array is missing', function (): void {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [])
            ->assertUnprocessable();

        expect($response->json('success'))->toBeFalse()
            ->and($response->json('message'))->toBe('Validation failed.')
            ->and($response->json('errors'))->toHaveKey('sales');
    });

    it('returns 422 when an item is missing required fields', function (): void {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    [
                        'local_id'           => (string) Str::uuid(),
                        'total_amount_cents' => 100,
                        'created_at'         => 1774789200000,
                        'items'              => [
                            [
                                'unit_price_cents' => 100,
                                'quantity'         => 1,
                                'subtotal_cents'   => 100,
                            ],
                        ],
                    ],
                ],
            ])
            ->assertUnprocessable();

        expect($response->json('success'))->toBeFalse()
            ->and($response->json('errors'))->toHaveKey('sales.0.items.0.product_id');
    });

    it('soft-deletes a pending sale when deleted_at is provided', function (): void {
        $product = Product::factory()->create();
        $localId = (string) Str::uuid();

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [array_merge(validSalePayload(productId: $product->id), ['local_id' => $localId])],
            ])
            ->assertOk();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    ['local_id' => $localId, 'deleted_at' => 1774794120000],
                ],
            ])
            ->assertOk();

        $created = $response->json('data.created.0');

        expect($response->json('success'))->toBeTrue()
            ->and($response->json('data.created'))->toHaveCount(1)
            ->and($created['local_id'])->toBe($localId)
            ->and($created['server_id'])->toBeInt()
            ->and($response->json('data.failed'))->toBeEmpty();

        expect(MobileSale::query()->where('local_id', $localId)->exists())->toBeFalse()
            ->and(MobileSale::withTrashed()->where('local_id', $localId)->exists())->toBeTrue();
    });

    it('preserves the device deleted_at timestamp on the server record', function (): void {
        $product = Product::factory()->create();
        $localId = (string) Str::uuid();

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [array_merge(validSalePayload(productId: $product->id), ['local_id' => $localId])],
            ])
            ->assertOk();

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [['local_id' => $localId, 'deleted_at' => 1774794120000]],
            ])
            ->assertOk();

        $mobileSale = MobileSale::withTrashed()->where('local_id', $localId)->first();

        expect($mobileSale->deleted_at->toDateString())->toBe('2026-03-29');
    });

    it('silently ignores deletion when the sale does not exist on the server', function (): void {
        $localId = (string) Str::uuid();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    ['local_id' => $localId, 'deleted_at' => 1774794120000],
                ],
            ])
            ->assertOk();

        expect($response->json('data.created'))->toHaveCount(1)
            ->and($response->json('data.created.0.local_id'))->toBe($localId)
            ->and($response->json('data.created.0.server_id'))->toBeNull()
            ->and($response->json('data.failed'))->toBeEmpty();
    });

    it('rejects deletion of a synced sale and reports it in failed[]', function (): void {
        $localId = (string) Str::uuid();

        MobileSale::factory()->create(['local_id' => $localId, 'status' => MobileSaleStatus::Synced]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    ['local_id' => $localId, 'deleted_at' => 1774794120000],
                ],
            ])
            ->assertOk();

        expect($response->json('data.created'))->toBeEmpty()
            ->and($response->json('data.failed'))->toHaveCount(1)
            ->and($response->json('data.failed.0.local_id'))->toBe($localId)
            ->and($response->json('data.failed.0.error'))->toBeString()->not->toBeEmpty();

        expect(MobileSale::withTrashed()->where('local_id', $localId)->whereNull('deleted_at')->exists())->toBeTrue();
    });

    it('handles a batch with mixed create, delete and failure', function (): void {
        $product = Product::factory()->create();
        $toCreateLocalId = (string) Str::uuid();
        $toDeleteLocalId = (string) Str::uuid();
        $syncedLocalId = (string) Str::uuid();

        MobileSale::factory()->create(['local_id' => $toDeleteLocalId, 'status' => MobileSaleStatus::Pending]);
        MobileSale::factory()->create(['local_id' => $syncedLocalId, 'status' => MobileSaleStatus::Synced]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    array_merge(validSalePayload(productId: $product->id), ['local_id' => $toCreateLocalId]),
                    ['local_id' => $toDeleteLocalId, 'deleted_at' => 1774794120000],
                    ['local_id' => $syncedLocalId, 'deleted_at' => 1774794120000],
                ],
            ])
            ->assertOk();

        expect($response->json('data.created'))->toHaveCount(2)
            ->and($response->json('data.created.0.local_id'))->toBe($toCreateLocalId)
            ->and($response->json('data.created.1.local_id'))->toBe($toDeleteLocalId)
            ->and($response->json('data.failed'))->toHaveCount(1)
            ->and($response->json('data.failed.0.local_id'))->toBe($syncedLocalId);
    });
});
