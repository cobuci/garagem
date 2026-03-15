<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
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
        $this->selectedCategoryId = 0;
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('sort_order')->orderBy('name')->get();
    }

    #[Computed]
    public function stats(): array
    {
        $products = Product::select('unit_cost', 'sale_price', 'stock_quantity')->get();

        $totalCost = $products->sum(fn ($p) => $p->unit_cost * ($p->stock_quantity ?? 0));
        $totalSale = $products->sum(fn ($p) => $p->sale_price * ($p->stock_quantity ?? 0));
        $totalProfit = $totalSale - $totalCost;

        return [
            'total_cost'   => $totalCost,
            'total_sale'   => $totalSale,
            'total_profit' => $totalProfit,
        ];
    }

    #[Computed]
    public function products(): LengthAwarePaginator|Paginator|\Illuminate\Support\Collection
    {
        if ($this->selectedCategoryId === 0) {
            return Product::latest()
                ->limit(10)
                ->get();
        }

        if (! $this->selectedCategoryId) {
            return Product::whereRaw('1=0')->paginate(10);
        }

        return Product::where('category_id', $this->selectedCategoryId)
            ->orderByRaw('stock_quantity > 0 desc')
            ->orderBy('name')
            ->paginate(10);
    }

    public function selectCategory(?int $id): void
    {
        $this->selectedCategoryId = $id;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.products.index');
    }
}
