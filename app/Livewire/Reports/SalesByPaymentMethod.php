<?php

namespace App\Livewire\Reports;

use App\Enums\PaymentMethod;
use App\Enums\Permission;
use App\Enums\SaleStatus;
use App\Models\Sale;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

class SalesByPaymentMethod extends Component
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

        $metrics = Sale::query()
            ->select('payment_method', DB::raw('SUM(total_amount) as total_sum'))
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->where('status', '!=', SaleStatus::Cancelled)
            ->groupBy('payment_method')
            ->orderBy('payment_method')
            ->get();

        $labels = $metrics->pluck('payment_method')->map(function ($method) {
            return PaymentMethod::fromRaw((string) $method)->label();
        })->all();

        $series = $metrics->pluck('total_sum')->map(function ($val) {
            return round((float) $val / 100, 2);
        })->all();

        $this->chartDataArray = [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    public function placeholder(): View
    {
        return view('livewire.reports.placeholders.sales-by-payment-method');
    }

    public function render(): View
    {
        return view('livewire.reports.sales-by-payment-method');
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
