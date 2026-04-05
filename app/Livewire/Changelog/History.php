<?php

namespace App\Livewire\Changelog;

use App\Models\Changelog;
use Illuminate\View\View;
use Livewire\Component;

class History extends Component
{
    public function render(): View
    {
        $changelogs = Changelog::query()
            ->with(['items' => fn ($q) => $q->orderBy('sort_order')])
            ->orderByDesc('released_at')
            ->get();

        return view('livewire.changelog.history', [
            'changelogs' => $changelogs,
        ]);
    }
}
