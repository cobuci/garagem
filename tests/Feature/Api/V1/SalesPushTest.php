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
        'created_at'         => '2026-03-29T10:00:00-03:00',
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
                        'created_at'         => '2026-03-29T10:00:00-03:00',
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

        expect($response->json('success'))->toBeTrue();
        expect($response->json('data.created'))->toHaveCount(1);
        expect($response->json('data.failed'))->toBeEmpty();

        $created = $response->json('data.created.0');
        expect($created['local_id'])->toBe($localId);
        expect($created['server_id'])->toBeInt();

        $mobileSale = MobileSale::query()->find($created['server_id']);
        expect($mobileSale)->not->toBeNull();
        expect($mobileSale->local_id)->toBe($localId);
        expect($mobileSale->total_amount_cents)->toBe(3980);
        expect($mobileSale->customer_name)->toBe('John Doe');
        expect($mobileSale->status)->toBe(MobileSaleStatus::Pending);

        expect(MobileSaleItem::query()->where('mobile_sale_id', $mobileSale->id)->count())->toBe(1);
    });

    it('creates a batch of sales and returns all in created[]', function (): void {
        $product = Product::factory()->create();

        $sales = collect(range(1, 3))->map(fn () => [
            'local_id'           => (string) Str::uuid(),
            'customer_id'        => null,
            'customer_name'      => 'Batch Customer',
            'total_amount_cents' => 1000,
            'created_at'         => now()->toIso8601String(),
            'items'              => [
                [
                    'product_id'       => $product->id,
                    'unit_price_cents' => 1000,
                    'quantity'         => 1,
                    'subtotal_cents'   => 1000,
                ],
            ],
        ])->all();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', ['sales' => $sales])
            ->assertOk();

        expect($response->json('data.created'))->toHaveCount(3);
        expect($response->json('data.failed'))->toBeEmpty();
        expect(MobileSale::query()->count())->toBe(3);
    });

    it('accepts a sale with null customer_id and a free-text customer name', function (): void {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    [
                        'local_id'           => (string) Str::uuid(),
                        'customer_id'        => null,
                        'customer_name'      => 'Maria Avulsa',
                        'total_amount_cents' => 500,
                        'created_at'         => now()->toIso8601String(),
                        'items'              => [
                            [
                                'product_id'       => $product->id,
                                'unit_price_cents' => 500,
                                'quantity'         => 1,
                                'subtotal_cents'   => 500,
                            ],
                        ],
                    ],
                ],
            ])
            ->assertOk();

        expect($response->json('data.created'))->toHaveCount(1);

        $serverId = $response->json('data.created.0.server_id');
        $mobileSale = MobileSale::query()->find($serverId);
        expect($mobileSale->customer_id)->toBeNull();
        expect($mobileSale->customer_name)->toBe('Maria Avulsa');
    });

    it('accepts a sale linked to an existing customer', function (): void {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    [
                        'local_id'           => (string) Str::uuid(),
                        'customer_id'        => $customer->id,
                        'customer_name'      => $customer->name,
                        'total_amount_cents' => 800,
                        'created_at'         => now()->toIso8601String(),
                        'items'              => [
                            [
                                'product_id'       => $product->id,
                                'unit_price_cents' => 800,
                                'quantity'         => 1,
                                'subtotal_cents'   => 800,
                            ],
                        ],
                    ],
                ],
            ])
            ->assertOk();

        $serverId = $response->json('data.created.0.server_id');
        expect(MobileSale::query()->find($serverId)->customer_id)->toBe($customer->id);
    });

    it('preserves the device created_at timestamp', function (): void {
        $product = Product::factory()->create();
        $deviceTs = '2026-01-15T08:30:00+00:00';

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    [
                        'local_id'           => (string) Str::uuid(),
                        'customer_id'        => null,
                        'customer_name'      => 'Test',
                        'total_amount_cents' => 100,
                        'created_at'         => $deviceTs,
                        'items'              => [
                            [
                                'product_id'       => $product->id,
                                'unit_price_cents' => 100,
                                'quantity'         => 1,
                                'subtotal_cents'   => 100,
                            ],
                        ],
                    ],
                ],
            ])
            ->assertOk();

        $serverId = $response->json('data.created.0.server_id');
        $mobileSale = MobileSale::query()->find($serverId);
        expect($mobileSale->device_created_at->toDateString())->toBe('2026-01-15');
    });

    it('reports a failed sale in failed[] without aborting the rest of the batch', function (): void {
        $product = Product::factory()->create();
        $duplicateId = (string) Str::uuid();

        // Pre-create a MobileSale with the same local_id to trigger a unique constraint violation
        MobileSale::factory()->create(['local_id' => $duplicateId]);

        $goodLocalId = (string) Str::uuid();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    [
                        'local_id'           => $duplicateId,
                        'customer_id'        => null,
                        'customer_name'      => 'Duplicate',
                        'total_amount_cents' => 100,
                        'created_at'         => now()->toIso8601String(),
                        'items'              => [
                            [
                                'product_id'       => $product->id,
                                'unit_price_cents' => 100,
                                'quantity'         => 1,
                                'subtotal_cents'   => 100,
                            ],
                        ],
                    ],
                    [
                        'local_id'           => $goodLocalId,
                        'customer_id'        => null,
                        'customer_name'      => 'Good',
                        'total_amount_cents' => 200,
                        'created_at'         => now()->toIso8601String(),
                        'items'              => [
                            [
                                'product_id'       => $product->id,
                                'unit_price_cents' => 200,
                                'quantity'         => 1,
                                'subtotal_cents'   => 200,
                            ],
                        ],
                    ],
                ],
            ])
            ->assertOk();

        expect($response->json('data.created'))->toHaveCount(1);
        expect($response->json('data.failed'))->toHaveCount(1);
        expect($response->json('data.created.0.local_id'))->toBe($goodLocalId);
        expect($response->json('data.failed.0.local_id'))->toBe($duplicateId);
        expect($response->json('data.failed.0.error'))->toBeString()->not->toBeEmpty();
    });

    it('returns 422 when sales array is missing', function (): void {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [])
            ->assertUnprocessable();

        expect($response->json('success'))->toBeFalse();
        expect($response->json('message'))->toBe('Validation failed.');
        expect($response->json('errors'))->toHaveKey('sales');
    });

    it('returns 422 when an item is missing required fields', function (): void {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/sales/push', [
                'sales' => [
                    [
                        'local_id'           => (string) Str::uuid(),
                        'total_amount_cents' => 100,
                        'created_at'         => now()->toIso8601String(),
                        'items'              => [
                            [
                                // product_id intentionally missing
                                'unit_price_cents' => 100,
                                'quantity'         => 1,
                                'subtotal_cents'   => 100,
                            ],
                        ],
                    ],
                ],
            ])
            ->assertUnprocessable();

        expect($response->json('success'))->toBeFalse();
        expect($response->json('errors'))->toHaveKey('sales.0.items.0.product_id');
    });
});
