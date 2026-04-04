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

        $data = Sale::query()
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
            ->get();

        $series = [];
        $days = [1, 2, 3, 4, 5, 6, 7]; // 1=Sun, 7=Sat

        // Initialize 24 hours
        for ($h = 0; $h < 24; $h++) {
            $hourLabel = str_pad($h, 2, '0', STR_PAD_LEFT) . 'h';
            $hourData = [];

            foreach ($days as $d) {
                $match = $data->where('dow', $d)->where('hour', $h)->first();
                $hourData[] = [
                    'x'     => $this->getDayLabel($d),
                    'y'     => $match ? round($match->revenue / 100, 2) : 0,
                    'sales' => $match ? (int) $match->sales_count : 0,
                ];
            }

            $series[] = [
                'name' => $hourLabel,
                'data' => $hourData,
            ];
        }

        $this->chartDataArray = [
            'series' => array_reverse($series), // Reverse to show 00h at bottom or top depending on preference
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
