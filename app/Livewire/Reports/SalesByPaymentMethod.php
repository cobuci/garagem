<?php

namespace App\Livewire\Reports;

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
            return match (strtolower($method)) {
                'credit_card' => __('reports.payment_methods.credit_card'),
                'debit_card'  => __('reports.payment_methods.debit_card'),
                'cash'        => __('reports.payment_methods.cash'),
                'pix'         => __('reports.payment_methods.pix'),
                default       => $method,
            };
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
            'last_7_days' => [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
            'this_month' => [
                Carbon::now()->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ],
            'last_6_months' => [
                Carbon::now()->subMonths(5)->startOfMonth()->startOfDay(),
                Carbon::now()->endOfMonth()->endOfDay(),
            ],
            default => [
                Carbon::now()->subDays(29)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
        };
    }
}
