<?php

namespace App\Livewire\Categories;

use App\Enums\Permission;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Delete extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public ?Category $category = null;

    public bool $deleteModal = false;

    public string $confirmation = '';

    #[On('category:delete')]
    public function confirmDeletion(Category $category): void
    {
        $this->authorize(Permission::DeleteCategory->value);

        if ($category->products()->exists()) {
            $this->notification()->error(
                title: __('categories.messages.error'),
                description: __('categories.messages.cannot_delete_with_products'),
            );

            return;
        }

        $this->category = $category;
        $this->confirmation = '';
        $this->deleteModal = true;
    }

    public function destroy(): void
    {
        $this->authorize(Permission::DeleteCategory->value);

        if (! $this->category) {
            return;
        }

        if ($this->category->products()->exists()) {
            $this->deleteModal = false;
            $this->category = null;
            $this->confirmation = '';

            $this->notification()->error(
                title: __('categories.messages.error'),
                description: __('categories.messages.cannot_delete_with_products'),
            );

            return;
        }

        if ($this->confirmation !== __('categories.delete_word')) {
            $this->notification()->error(
                title: __('categories.messages.error'),
                description: __('categories.messages.delete_incorrect'),
            );

            return;
        }

        $this->category->delete();

        $this->deleteModal = false;
        $this->category = null;
        $this->confirmation = '';

        $this->notification()->success(
            title: __('categories.messages.success'),
            description: __('categories.messages.deleted'),
        );

        $this->dispatch('category:deleted');
    }

    public function render(): View
    {
        return view('livewire.categories.delete');
    }
}
