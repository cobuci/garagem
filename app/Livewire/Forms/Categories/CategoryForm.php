<?php

namespace App\Livewire\Forms\Categories;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CategoryForm extends Form
{
    public ?Category $category = null;

    public string $name = '';

    public function setCategory(Category $category): void
    {
        $this->category = $category;
        $this->name = $category->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->ignore($this->category?->id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function store(): Category
    {
        $this->validate();

        $category = Category::create([
            'name'       => $this->name,
            'sort_order' => (int) Category::query()->max('sort_order') + 1,
        ]);

        $this->reset();

        return $category;
    }

    public function update(): void
    {
        $this->validate();

        $this->category->update([
            'name' => $this->name,
        ]);

        $this->reset();
    }
}
