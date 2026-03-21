<?php

namespace App\Livewire\Reports;

use App\Jobs\GenerateSystemReportJob;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class ExportReport extends Component
{
    use WireUiActions;

    public bool $showModal = false;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function export(): void
    {
        $this->validate([
            'startDate' => ['required', 'date', 'before_or_equal:endDate'],
            'endDate'   => ['required', 'date', 'after_or_equal:startDate'],
        ]);

        GenerateSystemReportJob::dispatch(
            Auth::user(),
            $this->startDate,
            $this->endDate,
        );

        $this->notification()->success(
            title: __('reports.export.success_title'),
            description: __('reports.export.success_description'),
        );

        $this->showModal = false;
    }

    public function render(): View
    {
        return view('livewire.reports.export-report');
    }
}
