@props(['selectedSale'])

<x-modal-card wire:model="showConfirmPaymentModal" title="{{ __('sales.confirm_payment') }}" max-width="md">
    <div class="space-y-4">
        <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ __('sales.confirm_payment_desc') }}
        </p>

        @if($selectedSale)
            <div class="bg-gray-50/80 dark:bg-gray-800/80 rounded-xl p-4 space-y-2.5 border border-gray-100 dark:border-gray-700/80">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('sales.customer') }}:</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $selectedSale->customer->name ?? __('sales.customer_placeholder') }}</span>
                </div>
                <div class="flex justify-between text-sm items-baseline">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('sales.total') }}:</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 tabular-nums text-base">R$ {{ number_format($selectedSale->total_amount, 2, ',', '.') }}</span>
                </div>
            </div>
        @endif
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-3">
            <x-button flat label="{{ __('sales.cancel') }}" x-on:click="close" />
            <x-button positive icon="check" label="{{ __('sales.yes_mark_as_paid') }}" wire:click="markAsPaid" spinner="markAsPaid" class="font-semibold shadow-xs" />
        </div>
    </x-slot>
</x-modal-card>
