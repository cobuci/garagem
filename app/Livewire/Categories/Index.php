<?php

namespace App\Livewire\Categories;

use App\Enums\Permission;
use App\Livewire\Forms\Categories\CategoryForm;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

/**
 * @property-read Collection<int, Category> $categories
 */
class Index extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public CategoryForm $form;

    public bool $showDrawer = false;

    public function mount(): void
    {
        $this->authorize(Permission::ViewCategory->value);
    }

    #[Computed, On('category:deleted')]
    public function categories(): Collection
    {
        return Category::query()
            ->select(['id', 'name', 'sort_order'])
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function create(): void
    {
        $this->authorize(Permission::CreateCategory->value);

        $this->form->reset();
        $this->showDrawer = true;
    }

    public function edit(Category $category): void
    {
        $this->authorize(Permission::EditCategory->value);

        $this->form->setCategory($category);
        $this->showDrawer = true;
    }

    public function save(): void
    {
        if ($this->form->category) {
            $this->authorize(Permission::EditCategory->value);
            $this->form->update();
            $this->finishSave(__('categories.messages.updated'));

            return;
        }

        $this->authorize(Permission::CreateCategory->value);
        $this->form->store();
        $this->finishSave(__('categories.messages.created'));
    }

    private function finishSave(string $description): void
    {
        $this->notification()->success(
            title: __('categories.messages.success'),
            description: $description,
        );

        $this->showDrawer = false;
        unset($this->categories);
    }

    public function updatedShowDrawer(bool $value): void
    {
        if (! $value) {
            $this->form->reset();
            $this->resetErrorBag();
        }
    }

    public function sort(int|string $id, int $position): void
    {
        $this->authorize(Permission::EditCategory->value);

        $ids = Category::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('id')
            ->map(fn ($categoryId): int => (int) $categoryId)
            ->values()
            ->all();

        $id = (int) $id;
        $ids = array_values(array_filter($ids, fn (int $categoryId): bool => $categoryId !== $id));
        array_splice($ids, $position, 0, [$id]);

        $cases = collect($ids)
            ->map(fn (int $categoryId, int $index): string => 'WHEN ' . $categoryId . ' THEN ' . ($index + 1))
            ->implode(' ');

        Category::query()
            ->whereIn('id', $ids)
            ->update(['sort_order' => DB::raw('CASE id ' . $cases . ' END')]);

        unset($this->categories);
    }

    public function confirmDelete(int $categoryId): void
    {
        $this->dispatch('category:delete', category: $categoryId);
    }

    public function render(): View
    {
        return view('livewire.categories.index');
    }
}
