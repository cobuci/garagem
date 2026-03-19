<?php

use App\Actions\Product\StockMovementAction;
use App\Models\Product;
use App\Models\ProductPurchase;

it('calculates average cost correctly on first purchase', function () {
    $product = Product::factory()->create([
        'stock_quantity' => 0,
        'unit_cost'      => 0,
    ]);

    $action = new StockMovementAction;
    $action->add([
        'product_id' => $product->id,
        'quantity'   => 10,
        'unit_cost'  => 50.00,
        'sale_price' => 80.00,
    ]);

    $product->refresh();

    expect($product->stock_quantity)->toBe(10)
        ->and((float) $product->unit_cost)->toBe(50.00)
        ->and((float) $product->sale_price)->toBe(80.00);
});

it('calculates weighted average cost correctly on subsequent purchases', function () {
    // Initial state: 10 units @ $50.00 = $500.00 total cost
    $product = Product::factory()->create([
        'stock_quantity' => 10,
        'unit_cost'      => 50.00,
        'sale_price'     => 80.00,
    ]);

    $action = new StockMovementAction;

    // New purchase: 10 units @ $70.00 = $700.00
    // Total cost: $500 + $700 = $1200
    // Total quantity: 10 + 10 = 20
    // Expected average cost: $1200 / 20 = $60.00
    $action->add([
        'product_id' => $product->id,
        'quantity'   => 10,
        'unit_cost'  => 70.00,
        'sale_price' => 100.00,
    ]);

    $product->refresh();

    expect($product->stock_quantity)->toBe(20)
        ->and((float) $product->unit_cost)->toBe(60.00)
        ->and((float) $product->sale_price)->toBe(100.00);

    // Check if purchase was recorded
    expect(ProductPurchase::where('product_id', $product->id)->count())->toBe(1);
});

it('removes stock correctly', function () {
    $product = Product::factory()->create([
        'stock_quantity' => 10,
    ]);

    $action = new StockMovementAction;
    $action->remove($product->id, 5);

    $product->refresh();

    expect($product->stock_quantity)->toBe(5);
});
