<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    #[Url(as: 't')]
    public string $activeTab = 'overview';

    public function mount(): void
    {
        $this->authorize(Permission::ViewReport->value);
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render(): View
    {
        return view('livewire.reports.index');
    }
}
