<?php

use App\Enums\SaleStatus;
use App\Models\AccountBalance;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

describe('GET /api/v1/dashboard', function (): void {
    it('requires authentication', function (): void {
        $this->getJson('/api/v1/dashboard')
            ->assertUnauthorized();
    });

    it('returns 200 with the expected structure', function (): void {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard')
            ->assertOk();

        expect($response->json('success'))->toBeTrue();
        expect($response->json('data'))->toHaveKeys(['balance', 'daily', 'monthly']);
        expect($response->json('data.balance'))->toHaveKey('current_balance');
        expect($response->json('data.daily'))->toHaveKeys(['sales', 'pending']);
        expect($response->json('data.monthly'))->toHaveKeys(['sales', 'pending', 'receivable']);
    });

    it('returns zeros when there are no sales', function (): void {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard')
            ->assertOk();

        expect($response->json('data.daily.sales'))->toBe(0);
        expect($response->json('data.daily.pending'))->toBe(0);
        expect($response->json('data.monthly.sales'))->toBe(0);
        expect($response->json('data.monthly.pending'))->toBe(0);
        expect($response->json('data.monthly.receivable'))->toBe(0);
    });

    it('returns the current account balance in cents', function (): void {
        // MoneyCast::set: passing 5000.0 stores 500000 cents in the database.
        // getRawOriginal returns 500000, which is what the API sends.
        AccountBalance::singleton()->update(['current_balance' => 5000.0]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard')
            ->assertOk();

        expect($response->json('data.balance.current_balance'))->toBe(500000);
    });

    it('returns daily paid sales in cents', function (): void {
        // MoneyCast::set: passing 200.0 stores 20000 cents. API returns 20000.
        Sale::factory()->create([
            'status'     => SaleStatus::Paid,
            'net_amount' => 200.0,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard')
            ->assertOk();

        expect($response->json('data.daily.sales'))->toBe(20000);
    });

    it('returns daily pending sales in cents', function (): void {
        Sale::factory()->create([
            'status'     => SaleStatus::Pending,
            'net_amount' => 150.0,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard')
            ->assertOk();

        expect($response->json('data.daily.pending'))->toBe(15000);
    });

    it('excludes cancelled sales from daily metrics', function (): void {
        Sale::factory()->create([
            'status'     => SaleStatus::Cancelled,
            'net_amount' => 500.0,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard')
            ->assertOk();

        expect($response->json('data.daily.sales'))->toBe(0);
        expect($response->json('data.daily.pending'))->toBe(0);
    });

    it('excludes sales from previous days in daily metrics', function (): void {
        Sale::factory()->create([
            'status'     => SaleStatus::Paid,
            'net_amount' => 300.0,
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard')
            ->assertOk();

        expect($response->json('data.daily.sales'))->toBe(0);
    });

    it('returns monthly paid sales in cents', function (): void {
        Sale::factory()->create([
            'status'     => SaleStatus::Paid,
            'net_amount' => 1000.0,
            'created_at' => now()->startOfMonth(),
        ]);

        Sale::factory()->create([
            'status'     => SaleStatus::Paid,
            'net_amount' => 500.0,
            'created_at' => now()->endOfMonth(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard')
            ->assertOk();

        expect($response->json('data.monthly.sales'))->toBe(150000);
    });

    it('returns monthly pending in cents and receivable as the sum', function (): void {
        Sale::factory()->create([
            'status'     => SaleStatus::Paid,
            'net_amount' => 800.0,
            'created_at' => now(),
        ]);

        Sale::factory()->create([
            'status'     => SaleStatus::Pending,
            'net_amount' => 200.0,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard')
            ->assertOk();

        expect($response->json('data.monthly.sales'))->toBe(80000);
        expect($response->json('data.monthly.pending'))->toBe(20000);
        expect($response->json('data.monthly.receivable'))->toBe(100000);
    });

    it('excludes sales from previous months in monthly metrics', function (): void {
        Sale::factory()->create([
            'status'     => SaleStatus::Paid,
            'net_amount' => 999.0,
            'created_at' => now()->subMonthsNoOverflow()->startOfMonth(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard')
            ->assertOk();

        expect($response->json('data.monthly.sales'))->toBe(0);
    });
});
