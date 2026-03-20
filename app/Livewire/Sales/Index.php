<?php

namespace App\Livewire\Sales;

use App\Enums\SaleStatus;
use App\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

/**
 * @property-read LengthAwarePaginator $sales
 * @property-read float $totalPendingAmount
 * @property-read ?Sale $selectedSale
 */
class Index extends Component
{
    use WireUiActions;
    use WithPagination;

    public ?string $status = 'pending';

    public ?int $selectedSaleId = null;

    public bool $showDetailsModal = false;

    public bool $showConfirmPaymentModal = false;

    public bool $showConfirmCancelModal = false;

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

    public function render(): View
    {
        return view('livewire.sales.index');
    }
}
