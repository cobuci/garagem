<?php

namespace App\Livewire\Products;

use App\Enums\Permission;
use App\Livewire\Forms\Products\PurchaseForm;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Purchase extends Component
{
    use AuthorizesRequests;
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
        $this->authorize(Permission::CreateProductPurchase->value);

        $this->purchaseDrawer = true;
    }

    public function updatedPurchaseDrawer($value): void
    {
        if ($value) {
            return;
        }

        $this->form->reset();
        $this->form->invoiceDate = now()->format('Y-m-d');
        $this->resetErrorBag();
    }

    public Collection $categories;

    #[Computed]
    public function products(): Collection
    {
        if (! $this->form->categoryId) {
            return new Collection;
        }

        return Product::where('category_id', $this->form->categoryId)
            ->orderBy('name')
            ->get()
            ->map(function (Product $product): Product {
                $suffix = collect([$product->brand, $product->weight])->filter()->implode(' · ');
                $product->label = $suffix ? "{$product->name} — {$suffix}" : $product->name;

                return $product;
            });
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

    #[Computed]
    public function profit(): float
    {
        return $this->form->profit();
    }

    public function save(): void
    {
        $this->authorize(Permission::CreateProductPurchase->value);

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
