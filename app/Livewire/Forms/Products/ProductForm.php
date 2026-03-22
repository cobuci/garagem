<?php

namespace App\Livewire\Forms\Products;

use App\Models\Product;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product = null;

    public ?int $categoryId = null;

    public string $name = '';

    public string $brand = '';

    public ?string $weightValue = null;

    public string $weightType = 'g';

    public string $upc = '';

    public $unitCost = null;

    public $salePrice = null;

    public $stockQuantity = 0;

    public ?string $expirationDate = null;

    public function rules(): array
    {
        return [
            'categoryId'     => ['required', 'exists:categories,id'],
            'name'           => ['required', 'string', 'max:255'],
            'brand'          => ['nullable', 'string', 'max:255'],
            'weightValue'    => ['required', 'numeric', 'min:0'],
            'weightType'     => ['required', 'string', 'in:ml,l,g,kg,unit'],
            'upc'            => ['nullable', 'string', 'max:255', 'unique:products,upc,' . $this->product?->id],
            'unitCost'       => ['nullable', 'numeric', 'min:0'],
            'salePrice'      => ['nullable', 'numeric', 'min:0'],
            'stockQuantity'  => ['nullable', 'integer', 'min:0'],
            'expirationDate' => ['nullable', 'date'],
        ];
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;

        $this->categoryId = $product->category_id;
        $this->name = $product->name;
        $this->brand = $product->brand ?? '';

        preg_match('/^([\d.]+)([a-z]+)$/', $product->weight, $matches);
        $this->weightValue = $matches[1] ?? '';
        $this->weightType = $matches[2] ?? 'g';

        $this->upc = $product->upc ?? '';
        $this->unitCost = $product->unit_cost;
        $this->salePrice = $product->sale_price;
        $this->stockQuantity = $product->stock_quantity;
        $this->expirationDate = $product->expiration_date?->format('Y-m-d');
    }

    public function store(): void
    {
        $this->validate();

        Product::create([
            'category_id' => $this->categoryId,
            'name'        => $this->name,
            'brand'       => $this->brand ?: null,
            'weight'      => $this->weightValue . $this->weightType,
            'upc'         => $this->upc ?: null,
        ]);

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $this->product->update([
            'category_id'     => $this->categoryId,
            'name'            => $this->name,
            'brand'           => $this->brand ?: null,
            'weight'          => $this->weightValue . $this->weightType,
            'upc'             => $this->upc ?: null,
            'unit_cost'       => $this->unitCost,
            'sale_price'      => $this->salePrice,
            'stock_quantity'  => $this->stockQuantity,
            'expiration_date' => $this->expirationDate,
        ]);
    }
}
