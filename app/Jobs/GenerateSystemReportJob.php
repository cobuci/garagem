<?php

namespace App\Jobs;

use App\Enums\Queue;
use App\Enums\TransactionType;
use App\Mail\SystemReportMail;
use App\Models\FinancialTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenerateSystemReportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $startDate,
        public string $endDate,
    ) {
        $this->onQueue(Queue::Default);
    }

    public function handle(): void
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $sales = Sale::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'paid')
            ->get();

        $totalRevenue = $sales->sum('total_amount');
        $totalDiscount = $sales->sum('discount_amount');
        $totalFees = $sales->sum('fee_amount');
        $netSales = $sales->sum('net_amount');

        $salesByPaymentMethod = $sales->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count'  => $group->count(),
                    'amount' => $group->sum('total_amount'),
                ];
            });

        $topProducts = SaleItem::query()
            ->select('product_id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(subtotal) as total_revenue')
            ->whereHas('sale', function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end])->where('status', 'paid');
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->limit(10)
            ->get();

        $transactions = FinancialTransaction::query()
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $inflow = $transactions->filter(fn ($t) => $t->type !== TransactionType::Purchase && $t->amount > 0)->sum('amount');
        $outflow = $transactions->filter(fn ($t) => $t->type === TransactionType::Purchase || $t->amount < 0)->sum('amount');

        $pdf = Pdf::loadView('pdf.system-report', [
            'user'                 => $this->user,
            'startDate'            => $this->startDate,
            'endDate'              => $this->endDate,
            'totalRevenue'         => $totalRevenue,
            'totalDiscount'        => $totalDiscount,
            'totalFees'            => $totalFees,
            'netSales'             => $netSales,
            'salesByPaymentMethod' => $salesByPaymentMethod,
            'topProducts'          => $topProducts,
            'inflow'               => $inflow,
            'outflow'              => $outflow,
            'generatedAt'          => now()->format('d/m/Y H:i'),
        ]);

        $fileName = "reports/system_report_{$this->user->id}_" . now()->timestamp . '.pdf';
        $pdfPath = storage_path("app/public/{$fileName}");

        Storage::disk('public')->put($fileName, $pdf->output());

        Mail::to($this->user->email)->send(new SystemReportMail(
            $this->user->name,
            $this->startDate,
            $this->endDate,
            $pdfPath,
        ));
    }
}
