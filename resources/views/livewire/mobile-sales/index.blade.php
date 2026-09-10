<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full" wire:poll.15s>
    @include('livewire.mobile-sales.components.stats', [
        'totalSales'         => $this->totalSales,
        'totalPending'       => $this->totalPending,
        'totalSynced'        => $this->totalSynced,
        'totalFailed'        => $this->totalFailed,
        'totalPendingAmount' => $this->totalPendingAmount,
        'totalAllAmount'     => $this->totalAllAmount,
    ])

    @include('livewire.mobile-sales.components.filters', [
        'status'       => $this->status,
        'search'       => $this->search,
        'totalSales'   => $this->totalSales,
        'totalPending' => $this->totalPending,
        'totalSynced'  => $this->totalSynced,
        'totalFailed'  => $this->totalFailed,
    ])

    @include('livewire.mobile-sales.components.card-list', [
        'mobileSales' => $this->mobileSales,
        'hasFilters'  => filled($this->search) || $this->status !== 'pending',
    ])

    @include('livewire.mobile-sales.components.details-modal', [
        'selectedMobileSale' => $this->selectedMobileSale,
    ])
</div>

