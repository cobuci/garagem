<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MobileSaleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SalesPushRequest;
use App\Models\MobileSale;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class SalesPushController extends Controller
{
    public function __invoke(SalesPushRequest $request): JsonResponse
    {
        $created = [];
        $failed = [];

        foreach ($request->validated('sales') as $saleData) {
            $localId = $saleData['local_id'];

            try {
                $serverId = DB::transaction(fn (): ?int => $this->upsert($saleData));

                $created[] = ['local_id' => $localId, 'server_id' => $serverId];
            } catch (Throwable $e) {
                $failed[] = ['local_id' => $localId, 'error' => $e->getMessage()];
            }
        }

        return response()->json([
            'success' => true,
            'data'    => compact('created', 'failed'),
        ]);
    }

    /** @param array<string, mixed> $data */
    private function upsert(array $data): ?int
    {
        $mobileSale = MobileSale::query()
            ->where('local_id', $data['local_id'])
            ->first();

        if (! empty($data['deleted_at'])) {
            return $this->processDeletion($mobileSale, $data['deleted_at']);
        }

        if (! $mobileSale instanceof MobileSale) {
            return $this->createMobileSale($data)->id;
        }

        if ($mobileSale->status === MobileSaleStatus::Synced) {
            throw new RuntimeException('Sale has already been finalised and cannot be updated.');
        }

        return $this->updateMobileSale($mobileSale, $data)->id;
    }

    private function processDeletion(?MobileSale $mobileSale, string $deletedAt): ?int
    {
        if (! $mobileSale instanceof MobileSale) {
            return null;
        }

        if ($mobileSale->status === MobileSaleStatus::Synced) {
            throw new RuntimeException('Sale has already been finalised and cannot be deleted.');
        }

        $mobileSale->update(['deleted_at' => $deletedAt]);

        return $mobileSale->id;
    }

    /** @param array<string, mixed> $data */
    private function createMobileSale(array $data): MobileSale
    {
        $mobileSale = MobileSale::query()->create([
            'local_id'           => $data['local_id'],
            'customer_id'        => $data['customer_id'],
            'customer_name'      => $data['customer_name'] ?? null,
            'total_amount_cents' => $data['total_amount_cents'],
            'device_created_at'  => $data['created_at'],
        ]);

        $this->syncItems($mobileSale, $data['items']);

        return $mobileSale;
    }

    /** @param array<string, mixed> $data */
    private function updateMobileSale(MobileSale $mobileSale, array $data): MobileSale
    {
        $mobileSale->update([
            'customer_id'        => $data['customer_id'],
            'customer_name'      => $data['customer_name'] ?? null,
            'total_amount_cents' => $data['total_amount_cents'],
        ]);

        $mobileSale->items()->delete();

        $this->syncItems($mobileSale, $data['items']);

        return $mobileSale;
    }

    /** @param array<int, array<string, mixed>> $items */
    private function syncItems(MobileSale $mobileSale, array $items): void
    {
        foreach ($items as $item) {
            $mobileSale->items()->create([
                'product_id'       => $item['product_id'],
                'unit_price_cents' => $item['unit_price_cents'],
                'quantity'         => $item['quantity'],
                'subtotal_cents'   => $item['subtotal_cents'],
            ]);
        }
    }
}
