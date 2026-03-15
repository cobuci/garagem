<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Delete extends Component
{
    use WireUiActions;

    public ?Product $product = null;

    public bool $deleteModal = false;

    public string $confirmation = '';

    #[On('product:delete')]
    public function confirmDeletion(Product $product): void
    {
        $this->product = $product;
        $this->confirmation = '';
        $this->deleteModal = true;
    }

    public function destroy(): void
    {
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
