<?php

namespace App\Livewire\Customers;

use App\Enums\SaleStatus;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

/**
 * @property-read LengthAwarePaginator $orders
 * @property-read float $totalSpent
 * @property-read float $totalDue
 * @property-read ?Sale $selectedSale
 */
class Show extends Component
{
    use WireUiActions;
    use WithPagination;

    public Customer $customer;

    public ?string $status = null;

    public ?int $selectedSaleId = null;

    public bool $showDetailsModal = false;

    public bool $showConfirmPaymentModal = false;

    public bool $showConfirmCancelModal = false;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function filterByStatus(?string $status): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    #[Computed, On(['customer:created', 'customer:updated', 'sale:created'])]
    public function orders(): LengthAwarePaginator
    {
        return $this->customer->sales()
            ->with(['items.product'])
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'paid' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(5);
    }

    #[Computed]
    public function totalSpent(): float
    {
        return (float) ($this->customer->sales()->where('status', SaleStatus::Paid)->sum('total_amount') / 100);
    }

    #[Computed]
    public function totalDue(): float
    {
        return (float) ($this->customer->sales()->where('status', SaleStatus::Pending)->sum('total_amount') / 100);
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
        return view('livewire.customers.show');
    }
}
