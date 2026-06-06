<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Enums\SaleStatus;
use App\Models\Sale;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class SalesByHourAndDay extends Component
{
    use AuthorizesRequests;

    public string $period = 'last_30_days';

    public array $chartDataArray = [];

    public function mount(): void
    {
        $this->authorize(Permission::ViewReport->value);
        $this->updateChartData();
    }

    public function updatedPeriod(): void
    {
        $this->updateChartData();
    }

    public function updateChartData(): void
    {
        [$startDate, $endDate] = $this->getPeriodConfig();

        $rows = Sale::query()
            ->selectRaw('
                DAYOFWEEK(created_at) as dow,
                HOUR(created_at) as hour,
                COUNT(*) as sales_count,
                SUM(total_amount) as revenue
            ')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->where('status', '!=', SaleStatus::Cancelled)
            ->groupBy('dow', 'hour')
            ->toBase()
            ->get();

        $byDayAndHour = [];

        foreach ($rows as $row) {
            $byDayAndHour[(int) $row->dow][(int) $row->hour] = [
                'revenue'     => (float) $row->revenue,
                'sales_count' => (int) $row->sales_count,
            ];
        }

        $series = [];

        for ($hour = 0; $hour < 24; $hour++) {
            $hourData = [];

            foreach (range(1, 7) as $day) {
                $hourData[] = [
                    'x'     => $this->getDayLabel($day),
                    'y'     => round(($byDayAndHour[$day][$hour]['revenue'] ?? 0) / 100, 2),
                    'sales' => $byDayAndHour[$day][$hour]['sales_count'] ?? 0,
                ];
            }

            $series[] = [
                'name' => sprintf('%02dh', $hour),
                'data' => $hourData,
            ];
        }

        $this->chartDataArray = [
            'series' => array_reverse($series),
        ];
    }

    private function getDayLabel(int $dow): string
    {
        return match ($dow) {
            1       => __('reports.days.sun'),
            2       => __('reports.days.mon'),
            3       => __('reports.days.tue'),
            4       => __('reports.days.wed'),
            5       => __('reports.days.thu'),
            6       => __('reports.days.fri'),
            7       => __('reports.days.sat'),
            default => '',
        };
    }

    public function placeholder(): View
    {
        return view('livewire.reports.placeholders.sales-by-hour-and-day');
    }

    public function render(): View
    {
        return view('livewire.reports.sales-by-hour-and-day');
    }

    private function getPeriodConfig(): array
    {
        return match ($this->period) {
            'today' => [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay(),
            ],
            'yesterday' => [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay(),
            ],
            'last_7_days' => [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
            'last_week' => [
                Carbon::now()->subWeek()->startOfWeek()->startOfDay(),
                Carbon::now()->subWeek()->endOfWeek()->endOfDay(),
            ],
            'this_month' => [
                Carbon::now()->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ],
            'last_month' => [
                Carbon::now()->subMonth()->startOfMonth()->startOfDay(),
                Carbon::now()->subMonth()->endOfMonth()->endOfDay(),
            ],
            'last_6_months' => [
                Carbon::now()->subMonths(5)->startOfMonth()->startOfDay(),
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
                Carbon::now()->subDays(29)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
        };
    }
}
