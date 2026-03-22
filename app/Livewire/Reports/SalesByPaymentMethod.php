<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Enums\SaleStatus;
use App\Models\Sale;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SalesByPaymentMethod extends Component
{
    use AuthorizesRequests;

    public string $period = 'last_30_days';

    public function mount(): void
    {
        $this->authorize(Permission::ViewReport->value);
    }

    #[Computed]
    public function chartData(): array
    {
        [$startDate, $endDate] = $this->getPeriodConfig();

        $metrics = Sale::query()
            ->select('payment_method', DB::raw('SUM(total_amount) as total_amount'))
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->where('status', SaleStatus::Paid)
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
        })->toArray();

        $series = $metrics->pluck('total_amount')->map(fn ($val) => round($val / 100, 2))->toArray();

        return [
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
                Carbon::today(),
                Carbon::today()->endOfDay(),
            ],
            'last_7_days' => [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
            'this_month' => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
            'last_6_months' => [
                Carbon::now()->subMonths(5)->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
            default => [
                Carbon::now()->subDays(29)->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
        };
    }
}
