<?php

namespace App\Livewire\Forms\Products;

use App\Models\Product;
use App\Models\ProductPurchase;
use Livewire\Form;

class PurchaseForm extends Form
{
    public ?int $categoryId = null;

    public ?int $productId = null;

    public $quantity = 1;

    public $unitCost = 0;

    public $totalCost = 0;

    public $salePrice = 0;

    public $expirationDate = null;

    public $invoiceDate = null;

    public $paymentDate = null;

    public $dueDate = null;

    public function rules(): array
    {
        return [
            'productId'      => ['required', 'exists:products,id'],
            'quantity'       => ['required', 'integer', 'min:1'],
            'unitCost'       => ['required', 'numeric', 'min:0'],
            'totalCost'      => ['required', 'numeric', 'min:0'],
            'salePrice'      => ['required', 'numeric', 'min:0'],
            'expirationDate' => ['nullable', 'date'],
            'invoiceDate'    => ['nullable', 'date'],
            'paymentDate'    => ['nullable', 'date'],
            'dueDate'        => ['nullable', 'date'],
        ];
    }

    public function updatedCategoryId(): void
    {
        $this->productId = null;
        $this->salePrice = 0;
    }

    public function updatedProductId($id): void
    {
        if ($id) {
            $product = Product::find($id);
            $this->salePrice = $product?->sale_price ?? 0;
            $this->unitCost = $product?->unit_cost ?? 0;
            $this->updatedUnitCost();
        } else {
            $this->salePrice = 0;
        }
    }

    public function updatedQuantity(): void
    {
        $this->totalCost = $this->unitCost * $this->quantity;
    }

    public function updatedUnitCost(): void
    {
        $this->totalCost = $this->unitCost * $this->quantity;
    }

    public function updatedTotalCost(): void
    {
        if ($this->quantity > 0) {
            $this->unitCost = $this->totalCost / $this->quantity;
        }
    }

    public function profit(): float
    {
        return (float) ($this->salePrice ?? 0) - (float) ($this->unitCost ?? 0);
    }

    public function store(): void
    {
        $this->validate();

        ProductPurchase::create([
            'product_id'   => $this->productId,
            'unit_cost'    => $this->unitCost,
            'total_cost'   => $this->totalCost,
            'quantity'     => $this->quantity,
            'invoice_date' => $this->invoiceDate,
            'payment_date' => $this->paymentDate,
            'due_date'     => $this->dueDate,
        ]);

        $product = Product::find($this->productId);
        $product->increment('stock_quantity', $this->quantity);

        $product->update([
            'unit_cost'  => $this->unitCost,
            'sale_price' => $this->salePrice,
        ]);

        if ($this->expirationDate) {
            $product->update(['expiration_date' => $this->expirationDate]);
        }
    }
}
