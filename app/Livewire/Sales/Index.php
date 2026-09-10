<?php

namespace App\Livewire\Sales;

use App\Enums\Permission as PermissionEnum;
use App\Enums\SaleStatus;
use App\Jobs\GenerateInvoiceJob;
use App\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WireUi\Traits\WireUiActions;

/**
 * @property-read LengthAwarePaginator $sales
 * @property-read float $totalPendingAmount
 * @property-read float $totalPaidAmount
 * @property-read float $totalAllAmount
 * @property-read int $pendingCount
 * @property-read int $paidCount
 * @property-read int $cancelledCount
 * @property-read int $allCount
 * @property-read ?Sale $selectedSale
 */
class Index extends Component
{
    use AuthorizesRequests;
    use WireUiActions;
    use WithPagination;

    public ?string $status = 'pending';

    public string $search = '';

    public ?int $selectedSaleId = null;

    public bool $showDetailsModal = false;

    public bool $showConfirmPaymentModal = false;

    public bool $showConfirmCancelModal = false;

    public function mount(): void
    {
        $this->authorize(PermissionEnum::ViewSale->value);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function filterByStatus(?string $status): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    #[Computed]
    public function sales(): LengthAwarePaginator
    {
        return Sale::query()
            ->with(['customer', 'items.product'])
            ->when($this->status, fn (Builder $query) => $query->where('status', $this->status))
            ->when(filled($this->search), function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    $subQuery->where('id', 'like', "%{$this->search}%")
                        ->orWhereHas(
                            'customer',
                            fn (Builder $customerQuery) => $customerQuery->where('name', 'like', "%{$this->search}%"),
                        );
                });
            })
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function totalPendingAmount(): float
    {
        return Sale::query()
            ->where('status', SaleStatus::Pending)
            ->sum('net_amount') / 100;
    }

    #[Computed]
    public function totalPaidAmount(): float
    {
        return Sale::query()
            ->where('status', SaleStatus::Paid)
            ->sum('net_amount') / 100;
    }

    #[Computed]
    public function totalAllAmount(): float
    {
        return Sale::query()
            ->whereIn('status', [SaleStatus::Pending, SaleStatus::Paid])
            ->sum('net_amount') / 100;
    }

    #[Computed]
    public function pendingCount(): int
    {
        return Sale::query()
            ->where('status', SaleStatus::Pending)
            ->count();
    }

    #[Computed]
    public function paidCount(): int
    {
        return Sale::query()
            ->where('status', SaleStatus::Paid)
            ->count();
    }

    #[Computed]
    public function cancelledCount(): int
    {
        return Sale::query()
            ->where('status', SaleStatus::Cancelled)
            ->count();
    }

    #[Computed]
    public function allCount(): int
    {
        return Sale::query()->count();
    }

    #[Computed]
    public function selectedSale(): ?Sale
    {
        if (! $this->selectedSaleId) {
            return null;
        }

        return Sale::with(['customer', 'items.product'])->find($this->selectedSaleId);
    }

    public function showDetails(int $saleId): void
    {
        $this->selectedSaleId = $saleId;
        $this->showDetailsModal = true;
    }

    public function confirmMarkAsPaid(int $saleId): void
    {
        $this->selectedSaleId = $saleId;
        $this->showConfirmPaymentModal = true;
    }

    public function markAsPaid(): void
    {
        $this->authorize(PermissionEnum::EditSale->value);

        if (! $this->selectedSaleId) {
            return;
        }

        $sale = Sale::query()->find($this->selectedSaleId);

        if (! $sale instanceof Sale) {
            return;
        }

        if ($sale->status !== SaleStatus::Pending) {
            $this->notification()->error(__('sales.only_pending_can_be_paid'));

            return;
        }

        $sale->update(['status' => SaleStatus::Paid]);
        $this->showDetailsModal = false;
        $this->showConfirmPaymentModal = false;
        $this->notification()->success(__('sales.mark_as_paid_success'));
    }

    public function confirmCancelSale(int $saleId): void
    {
        $this->selectedSaleId = $saleId;
        $this->showConfirmCancelModal = true;
    }

    public function cancelSale(): void
    {
        $this->authorize(PermissionEnum::EditSale->value);

        if (! $this->selectedSaleId) {
            return;
        }

        $sale = Sale::query()->find($this->selectedSaleId);

        if (! $sale instanceof Sale) {
            return;
        }

        if ($sale->status === SaleStatus::Cancelled) {
            $this->notification()->error(__('sales.already_cancelled'));

            return;
        }

        $sale->update(['status' => SaleStatus::Cancelled]);
        $this->showDetailsModal = false;
        $this->showConfirmCancelModal = false;
        $this->notification()->success(__('sales.cancel_sale_success'));
    }

    public function downloadInvoice(int $saleId): ?StreamedResponse
    {
        $sale = Sale::find($saleId);

        if (! $sale) {
            return null;
        }

        $this->authorize(PermissionEnum::ViewSale->value, $sale);

        if ($sale->invoice_status === 'ready' && $sale->invoice_path && Storage::exists($sale->invoice_path)) {
            return Storage::download($sale->invoice_path, "{$sale->id}.pdf");
        }

        $sale->update(['invoice_status' => 'generating']);
        GenerateInvoiceJob::dispatch($sale);

        return null;
    }

    public function downloadInvoicePng(int $saleId): ?StreamedResponse
    {
        $sale = Sale::find($saleId);

        if (! $sale) {
            return null;
        }

        $this->authorize(PermissionEnum::ViewSale->value, $sale);

        if ($sale->invoice_status === 'ready' && $sale->invoice_png_path && Storage::exists($sale->invoice_png_path)) {
            return Storage::download($sale->invoice_png_path, "{$sale->id}.png");
        }

        $sale->update(['invoice_status' => 'generating']);
        GenerateInvoiceJob::dispatch($sale);

        return null;
    }

    public function render(): View
    {
        return view('livewire.sales.index');
    }
}
