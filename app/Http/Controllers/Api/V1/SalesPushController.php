<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SalesPushRequest;
use App\Models\MobileSale;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
                DB::transaction(function () use ($saleData, $localId, &$created): void {
                    $mobileSale = MobileSale::query()->create([
                        'local_id'           => $localId,
                        'customer_id'        => $saleData['customer_id'] ?? null,
                        'customer_name'      => $saleData['customer_name'] ?? null,
                        'total_amount_cents' => $saleData['total_amount_cents'],
                        'device_created_at'  => $saleData['created_at'],
                    ]);

                    foreach ($saleData['items'] as $item) {
                        $mobileSale->items()->create([
                            'product_id'       => $item['product_id'],
                            'unit_price_cents' => $item['unit_price_cents'],
                            'quantity'         => $item['quantity'],
                            'subtotal_cents'   => $item['subtotal_cents'],
                        ]);
                    }

                    $created[] = [
                        'local_id'  => $localId,
                        'server_id' => $mobileSale->id,
                    ];
                });
            } catch (Throwable $e) {
                $failed[] = [
                    'local_id' => $localId,
                    'error'    => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'created' => $created,
                'failed'  => $failed,
            ],
        ]);
    }
}
