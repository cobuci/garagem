<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Enums\SaleStatus;
use App\Models\Sale;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class AverageTicketEvolution extends Component
{
    use AuthorizesRequests;

    public string $period = 'last_6_months';

    public array $chartDataArray = [];

    public function rules(): array
    {
        return [
            'period' => [
                'required',
                'in:last_3_months,last_6_months,last_12_months,this_year,last_year',
            ],
        ];
    }

    public function mount(): void
    {
        $this->authorize(Permission::ViewReport->value);
        $this->updateChartData();
    }

    public function updatedPeriod(): void
    {
        $this->validate();
        $this->updateChartData();
    }

    public function updateChartData(): void
    {
        [$startDate, $endDate] = $this->getPeriodConfig();

        $rows = Sale::query()
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month_key,
                DATE_FORMAT(created_at, '%b %y') as month_label,
                ROUND(AVG(total_amount) / 100, 2) as avg_ticket,
                COUNT(*) as total_sales
            ")
            ->where('status', '!=', SaleStatus::Cancelled)
            ->where('is_gift', false)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b %y')")
            ->orderByRaw("DATE_FORMAT(created_at, '%Y-%m') ASC")
            ->get();

        $this->chartDataArray = [
            'categories' => $rows->pluck('month_label')->toArray(),
            'series'     => [
                [
                    'name' => __('reports.average_ticket_evolution.avg_ticket'),
                    'data' => $rows->pluck('avg_ticket')->map(fn ($v) => (float) $v)->toArray(),
                ],
            ],
        ];
    }

    public function placeholder(): View
    {
        return view('livewire.reports.placeholders.average-ticket-evolution');
    }

    public function render(): View
    {
        return view('livewire.reports.average-ticket-evolution');
    }

    private function getPeriodConfig(): array
    {
        return match ($this->period) {
            'last_3_months' => [
                Carbon::now()->subMonths(2)->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ],
            'last_6_months' => [
                Carbon::now()->subMonths(5)->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ],
            'last_12_months' => [
                Carbon::now()->subMonths(11)->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ],
            'this_year' => [
                Carbon::now()->startOfYear()->startOfDay(),
                Carbon::now()->endOfYear()->endOfDay(),
            ],
            'last_year' => [
                Carbon::now()->subYear()->startOfYear()->startOfDay(),
                Carbon::now()->subYear()->endOfYear()->endOfDay(),
            ],
            default => [
                Carbon::now()->subMonths(5)->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ],
        };
    }
}
