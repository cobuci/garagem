<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full"
     @if($this->selectedSale && $this->selectedSale->invoice_status === 'generating') wire:poll.1s @endif>
    @include('livewire.sales.components.stats', [
        'totalPendingAmount' => $this->totalPendingAmount,
        'pendingCount'       => $this->pendingCount,
        'totalPaidAmount'    => $this->totalPaidAmount,
        'paidCount'          => $this->paidCount,
        'totalAllAmount'     => $this->totalAllAmount,
        'totalSalesCount'    => $this->allCount,
    ])
    @include('livewire.sales.components.filters', [
        'status'         => $status,
        'pendingCount'   => $this->pendingCount,
        'paidCount'      => $this->paidCount,
        'cancelledCount' => $this->cancelledCount,
        'allCount'       => $this->allCount,
    ])
    @include('livewire.sales.components.table', ['sales' => $this->sales])

    @include('livewire.sales.components.details-modal', ['selectedSale' => $this->selectedSale])
    @include('livewire.sales.components.confirm-payment-modal', ['selectedSale' => $this->selectedSale])
    @include('livewire.sales.components.confirm-cancel-modal', ['selectedSale' => $this->selectedSale])
</div>
