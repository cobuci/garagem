<?php

namespace App\Livewire\Changelog;

use App\Enums\Permission;
use App\Models\Changelog;
use App\Models\ChangelogItem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    #[Modelable]
    public ?int $changelogId = null;

    public string $version = '';

    public string $title = '';

    public string $releasedAt = '';

    public array $items = [];

    public function mount(): void
    {
        $this->authorize(Permission::ManageChangelog->value);

        if ($this->changelogId !== null) {
            $changelog = Changelog::with('items')->findOrFail($this->changelogId);
            $this->version = $changelog->version;
            $this->title = $changelog->title;
            $this->releasedAt = $changelog->released_at->format('Y-m-d');
            $this->items = $changelog->items->map(fn (ChangelogItem $item) => [
                'id'          => $item->id,
                'title'       => $item->title,
                'description' => $item->description,
                'image_path'  => $item->image_path,
                'upload'      => null,
            ])->all();
        }

        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'id'          => null,
            'title'       => '',
            'description' => '',
            'image_path'  => null,
            'upload'      => null,
        ];
    }

    public function removeItem(int $index): void
    {
        array_splice($this->items, $index, 1);
        $this->items = array_values($this->items);
    }

    public function save(): void
    {
        $this->authorize(Permission::ManageChangelog->value);

        $this->validate([
            'version'             => 'required|string|max:20',
            'title'               => 'required|string|max:255',
            'releasedAt'          => 'required|date',
            'items'               => 'required|array|min:1',
            'items.*.title'       => 'required|string|max:255',
            'items.*.description' => 'required|string',
            'items.*.upload'      => 'nullable|image|max:2048',
        ]);

        $changelog = Changelog::updateOrCreate(
            ['id' => $this->changelogId],
            [
                'version'     => $this->version,
                'title'       => $this->title,
                'released_at' => $this->releasedAt,
            ],
        );

        $existingIds = [];

        foreach ($this->items as $index => $itemData) {
            $imagePath = $itemData['image_path'] ?? null;

            if (! empty($itemData['upload'])) {
                $upload = $itemData['upload'];
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $upload->store('changelog', 'public');
            }

            $item = ChangelogItem::updateOrCreate(
                ['id' => $itemData['id'] ?? null, 'changelog_id' => $changelog->id],
                [
                    'title'       => $itemData['title'],
                    'description' => $itemData['description'],
                    'image_path'  => $imagePath,
                    'sort_order'  => $index,
                ],
            );

            $existingIds[] = $item->id;
        }

        ChangelogItem::query()
            ->where('changelog_id', $changelog->id)
            ->whereNotIn('id', $existingIds)
            ->each(function (ChangelogItem $item) {
                if ($item->image_path) {
                    Storage::disk('public')->delete($item->image_path);
                }
                $item->delete();
            });

        $this->dispatch('changelog-saved');
    }

    public function render(): View
    {
        return view('livewire.changelog.form');
    }
}
