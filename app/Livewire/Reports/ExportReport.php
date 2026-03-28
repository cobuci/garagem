<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Jobs\GenerateSystemReportJob;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class ExportReport extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public bool $showModal = false;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function mount(): void
    {
        $this->authorize(Permission::ViewReport->value);

        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function applyPeriod(string $period): void
    {
        [$this->startDate, $this->endDate] = match ($period) {
            'today'        => [now()->format('Y-m-d'), now()->format('Y-m-d')],
            'yesterday'    => [now()->subDay()->format('Y-m-d'), now()->subDay()->format('Y-m-d')],
            'last_7_days'  => [now()->subDays(6)->format('Y-m-d'), now()->format('Y-m-d')],
            'last_30_days' => [now()->subDays(29)->format('Y-m-d'), now()->format('Y-m-d')],
            'this_month'   => [now()->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')],
            'last_month'   => [now()->subMonth()->startOfMonth()->format('Y-m-d'), now()->subMonth()->endOfMonth()->format('Y-m-d')],
            'this_year'    => [now()->startOfYear()->format('Y-m-d'), now()->format('Y-m-d')],
            'last_year'    => [now()->subYear()->startOfYear()->format('Y-m-d'), now()->subYear()->endOfYear()->format('Y-m-d')],
            default        => [$this->startDate, $this->endDate],
        };
    }

    public function export(): void
    {
        $this->authorize(Permission::ViewReport->value);

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
