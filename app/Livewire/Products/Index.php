<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?int $selectedCategoryId = null;

    public function mount(): void
    {
        $this->selectedCategoryId = Category::first()?->id;
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::all();
    }

    #[Computed]
    public function products(): LengthAwarePaginator
    {
        if (! $this->selectedCategoryId) {
            return Product::whereRaw('1=0')->paginate(10);
        }

        return Product::where('category_id', $this->selectedCategoryId)
            ->orderByRaw('stock_quantity > 0 desc')
            ->orderBy('name')
            ->paginate(10);
    }

    public function selectCategory(int $id): void
    {
        $this->selectedCategoryId = $id;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.products.index');
    }
}
