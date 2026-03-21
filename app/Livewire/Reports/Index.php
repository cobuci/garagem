<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize(Permission::ViewReport->value);
    }

    public function render(): View
    {
        return view('livewire.reports.index');
    }
}
