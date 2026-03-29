<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    @include('livewire.mobile-sales.components.stats', [
        'totalSales'   => $this->totalSales,
        'totalPending' => $this->totalPending,
    ])
    @include('livewire.mobile-sales.components.card-list', ['mobileSales' => $this->mobileSales])

    @include('livewire.mobile-sales.components.details-modal', ['selectedMobileSale' => $this->selectedMobileSale])
</div>
