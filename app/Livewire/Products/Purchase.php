<?php

namespace App\Livewire\Products;

use App\Livewire\Forms\Products\PurchaseForm;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Purchase extends Component
{
    use WireUiActions;

    public PurchaseForm $form;

    public bool $purchaseDrawer = false;

    public function mount(): void
    {
        $this->form->invoiceDate = now()->format('Y-m-d');
    }

    #[On('purchase:open')]
    public function openDrawer(): void
    {
        $this->purchaseDrawer = true;
    }

    public function updatedPurchaseDrawer($value): void
    {
        if (! $value) {
            $this->form->reset();
            $this->form->invoiceDate = now()->format('Y-m-d');
        }
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }

    #[Computed]
    public function products(): Collection
    {
        if (! $this->form->categoryId) {
            return new Collection;
        }

        return Product::where('category_id', $this->form->categoryId)->orderBy('name')->get();
    }

    public function updatedFormCategoryId(): void
    {
        $this->form->updatedCategoryId();
    }

    public function updatedFormProductId($id): void
    {
        $this->form->updatedProductId($id);
    }

    public function updatedFormQuantity(): void
    {
        $this->form->updatedQuantity();
    }

    public function updatedFormUnitCost(): void
    {
        $this->form->updatedUnitCost();
    }

    public function updatedFormTotalCost(): void
    {
        $this->form->updatedTotalCost();
    }

    public function updatedFormSalePrice(): void
    {
        //
    }

    #[Computed]
    public function profit(): float
    {
        return $this->form->profit();
    }

    public function save(): void
    {
        $this->form->store();

        $this->purchaseDrawer = false;

        $this->notification()->success(
            title: __('products.success_purchase'),
        );

        $this->dispatch('product:updated')->to(Index::class);
    }

    public function render(): View
    {
        return view('livewire.products.purchase');
    }
}
