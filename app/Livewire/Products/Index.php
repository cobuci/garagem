<?php

namespace App\Livewire\Products;

use App\Enums\Permission;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Index extends Component
{
    use AuthorizesRequests;
    use WireUiActions;
    use WithPagination;

    public ?int $selectedCategoryId = null;

    public array $skippedCategories = [];

    public function mount(): void
    {
        $this->authorize(Permission::ViewProduct->value);

        $this->selectedCategoryId = 0;
        $this->skippedCategories = Setting::singleton()->skipped_categories ?? [];
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('sort_order')->orderBy('name')->get();
    }

    #[Computed]
    public function stats(): array
    {
        $stats = Product::query()
            ->when(! empty($this->skippedCategories), fn ($query) => $query->whereNotIn('category_id', $this->skippedCategories))
            ->where('stock_quantity', '>', 0)
            ->selectRaw('SUM(unit_cost * stock_quantity) as total_cost')
            ->selectRaw('SUM(sale_price * stock_quantity) as total_sale')
            ->toBase()
            ->first();

        $totalCost = ((float) ($stats->total_cost ?? 0)) / 100;
        $totalSale = ((float) ($stats->total_sale ?? 0)) / 100;
        $totalProfit = $totalSale - $totalCost;

        return [
            'total_cost'   => $totalCost,
            'total_sale'   => $totalSale,
            'total_profit' => $totalProfit,
        ];
    }

    #[Computed, On(['product:created', 'product:updated'])]
    public function products(): LengthAwarePaginator|Paginator|\Illuminate\Support\Collection
    {
        if ($this->selectedCategoryId === 0) {
            return Product::orderByDesc('id')
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
