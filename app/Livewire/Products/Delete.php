<?php

namespace App\Livewire\Products;

use App\Enums\Permission;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Delete extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public ?Product $product = null;

    public bool $deleteModal = false;

    public string $confirmation = '';

    #[On('product:delete')]
    public function confirmDeletion(Product $product): void
    {
        $this->authorize(Permission::DeleteProduct->value);

        $this->product = $product;
        $this->confirmation = '';
        $this->deleteModal = true;
    }

    public function destroy(): void
    {
        $this->authorize(Permission::DeleteProduct->value);

        if ($this->confirmation !== __('products.delete_word')) {
            $this->notification()->error(
                title: __('products.delete_incorrect'),
            );

            return;
        }

        $this->product->delete();

        $this->deleteModal = false;

        $this->notification()->success(
            title: __('products.success_deleted'),
        );

        $this->dispatch('product:updated');
    }

    public function render(): View
    {
        return view('livewire.products.delete');
    }
}
