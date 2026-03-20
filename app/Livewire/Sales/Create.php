<?php

namespace App\Livewire\Sales;

use App\Livewire\Forms\SaleForm;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Create extends Component
{
    use WireUiActions;

    public SaleForm $form;

    public ?int $selectedCategoryId = null;

    public string $search = '';

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }

    #[Computed]
    public function products(): Collection
    {
        if (empty($this->search) && empty($this->selectedCategoryId)) {
            return new Collection;
        }

        return Product::query()
            ->when($this->selectedCategoryId, fn ($q) => $q->where('category_id', $this->selectedCategoryId))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->with('category')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function customers(): Collection
    {
        return Customer::orderBy('name')->get();
    }

    public function addItem(int $productId): void
    {
        $this->form->addItem($productId);
    }

    public function removeItem(int $productId): void
    {
        $this->form->removeItem($productId);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $this->form->updateQuantity($productId, $quantity);
    }

    #[Computed]
    public function subtotal(): int
    {
        return $this->form->subtotal();
    }

    #[Computed]
    public function feePercentage(): float
    {
        return $this->form->feePercentage();
    }

    #[Computed]
    public function discountInCents(): int
    {
        return $this->form->discountInCents();
    }

    #[Computed]
    public function feeAmount(): int
    {
        return $this->form->feeAmount();
    }

    #[Computed]
    public function totalAmount(): int
    {
        return $this->form->totalAmount();
    }

    #[Computed]
    public function netAmount(): int
    {
        return $this->form->netAmount();
    }

    public function save(): void
    {
        if (empty($this->form->items)) {
            $this->notification()->error(__('sales.at_least_one_product'));

            return;
        }

        $this->form->store();

        $this->notification()->success(__('sales.sale_success'));
    }

    public function render(): View
    {
        return view('livewire.sales.create');
    }
}
