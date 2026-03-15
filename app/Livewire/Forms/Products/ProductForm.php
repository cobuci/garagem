<?php

namespace App\Livewire\Forms\Products;

use App\Models\Product;
use Livewire\Form;

class ProductForm extends Form
{
    public ?int $categoryId = null;

    public string $name = '';

    public string $brand = '';

    public ?string $weightValue = null;

    public string $weightType = 'g';

    public string $upc = '';

    public function rules(): array
    {
        return [
            'categoryId'  => ['required', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'brand'       => ['nullable', 'string', 'max:255'],
            'weightValue' => ['required', 'numeric', 'min:0'],
            'weightType'  => ['required', 'string', 'in:ml,l,g,kg,unit'],
            'upc'         => ['nullable', 'string', 'max:255', 'unique:products,upc'],
        ];
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
}
