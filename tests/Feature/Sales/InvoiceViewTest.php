<?php

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('invoice view includes product brand and weight for each item', function () {
    $product = Product::factory()->create([
        'name'   => 'Produto Teste',
        'brand'  => 'Marca Exemplo',
        'weight' => '500g',
    ]);

    $sale = Sale::factory()->create();

    SaleItem::factory()->create([
        'sale_id'    => $sale->id,
        'product_id' => $product->id,
        'quantity'   => 2,
        'unit_price' => 10.00,
        'subtotal'   => 20.00,
    ]);

    $sale->load(['customer', 'items.product']);

    $html = view('pdf.invoice', [
        'sale'     => $sale,
        'settings' => Setting::singleton(),
    ])->render();

    expect($html)
        ->toContain('Produto Teste')
        ->toContain('Marca Exemplo')
        ->toContain('500g')
        ->toContain('Marca Exemplo · 500g');
});

test('invoice view shows weight without brand when brand is missing', function () {
    $product = Product::factory()->create([
        'name'   => 'Produto Sem Marca',
        'brand'  => null,
        'weight' => '1kg',
    ]);

    $sale = Sale::factory()->create();

    SaleItem::factory()->create([
        'sale_id'    => $sale->id,
        'product_id' => $product->id,
    ]);

    $sale->load(['customer', 'items.product']);

    $html = view('pdf.invoice', [
        'sale'     => $sale,
        'settings' => Setting::singleton(),
    ])->render();

    expect($html)
        ->toContain('Produto Sem Marca')
        ->toContain('1kg')
        ->not->toContain(' · 1kg');
});
