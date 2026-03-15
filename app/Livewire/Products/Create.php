<?php

namespace App\Livewire\Products;

use App\Livewire\Forms\Products\ProductForm;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Create extends Component
{
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
