<?php

namespace App\Livewire\Products;

use App\Enums\Permission;
use App\Livewire\Forms\Products\ProductForm;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Create extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public ProductForm $form;

    public bool $createDrawer = false;

    public Collection $categories;

    #[On('product:open-create')]
    public function openDrawer(): void
    {
        $this->authorize(Permission::CreateProduct->value);

        $this->form->reset();
        $this->resetErrorBag();
        $this->createDrawer = true;
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

    public function updatedCreateDrawer(bool $value): void
    {
        if (! $value) {
            $this->form->reset();
            $this->resetErrorBag();
        }
    }

    public function render(): View
    {
        return view('livewire.products.create');
    }
}
