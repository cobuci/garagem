<?php

namespace App\Livewire\Changelog;

use App\Models\Changelog;
use App\Models\ChangelogItem;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\View\View;
use Livewire\Component;

class Modal extends Component
{
    public bool $isOpen = false;

    public int $currentStep = 0;

    public int $totalSteps = 0;

    public array $allItems = [];

    public array $changelogIds = [];

    public function mount(#[CurrentUser] User $user): void
    {
        $unseenChangelogs = Changelog::query()
            ->unseenBy($user)
            ->with(['items' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('released_at')
            ->get();

        if ($unseenChangelogs->isEmpty()) {
            return;
        }

        $this->changelogIds = $unseenChangelogs->pluck('id')->all();

        $this->allItems = $unseenChangelogs
            ->flatMap(fn (Changelog $changelog) => $changelog->items->map(fn (ChangelogItem $item) => [
                'id'                => $item->id,
                'changelog_id'      => $item->changelog_id,
                'title'             => $item->title,
                'description'       => $item->description,
                'image_path'        => $item->image_path,
                'sort_order'        => $item->sort_order,
                'changelog_version' => $changelog->version,
                'changelog_title'   => $changelog->title,
            ]))
            ->values()
            ->all();

        $this->totalSteps = count($this->allItems);

        if ($this->totalSteps > 0) {
            $this->isOpen = true;
        }
    }

    public function next(): void
    {
        if ($this->currentStep < $this->totalSteps - 1) {
            $this->currentStep++;
        } else {
            $this->dismiss();
        }
    }

    public function previous(): void
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
        }
    }

    public function dismiss(): void
    {
        $user = auth()->user();

        $now = now();

        $syncData = collect($this->changelogIds)
            ->mapWithKeys(fn (int $id) => [$id => ['seen_at' => $now]])
            ->all();

        $user->seenChangelogs()->syncWithoutDetaching($syncData);

        $this->isOpen = false;
    }

    public function reopen(): void
    {
        if ($this->totalSteps > 0) {
            $this->currentStep = 0;
            $this->isOpen = true;
        }
    }

    public function render(): View
    {
        $currentItem = $this->totalSteps > 0 ? $this->allItems[$this->currentStep] : null;

        return view('livewire.changelog.modal', [
            'currentItem' => $currentItem,
            'isFirst'     => $this->currentStep === 0,
            'isLast'      => $this->currentStep === $this->totalSteps - 1,
        ]);
    }
}
