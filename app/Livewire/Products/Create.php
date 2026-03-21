<?php

namespace App\Livewire\Products;

use App\Enums\Permission;
use App\Livewire\Forms\Products\ProductForm;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Create extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public ProductForm $form;

    public bool $createDrawer = false;

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('sort_order')->orderBy('name')->get();
    }

    public function create(): void
    {
        $this->authorize(Permission::CreateProduct->value);

        $this->form->store();

        $this->createDrawer = false;

        $this->notification()->success(
            title: __('products.success_created'),
        );

        $this->dispatch('product:created');
    }

    public function render(): View
    {
        return view('livewire.products.create');
    }
}
