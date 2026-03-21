<?php

namespace App\Livewire\Products;

use App\Enums\Permission;
use App\Livewire\Forms\Products\ProductForm;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Edit extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public ProductForm $form;

    public bool $editDrawer = false;

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('sort_order')->orderBy('name')->get();
    }

    #[On('product:edit')]
    public function edit(Product $product): void
    {
        $this->authorize(Permission::EditProduct->value);

        $this->form->setProduct($product);

        $this->editDrawer = true;
    }

    public function update(): void
    {
        $this->authorize(Permission::EditProduct->value);

        $this->form->update();

        $this->editDrawer = false;

        $this->notification()->success(
            title: __('products.success_updated'),
        );

        $this->dispatch('product:updated');
    }

    public function render(): View
    {
        return view('livewire.products.edit');
    }
}
