@props(['selectedSale'])

<x-modal-card wire:model="showConfirmCancelModal" title="{{ __('sales.confirm_cancel') }}" max-width="md">
    <div class="space-y-4">
        <div class="p-3 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200/80 dark:border-red-800/60 flex items-start gap-2.5">
            <x-icon name="exclamation-triangle" class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" />
            <p class="text-xs text-red-700 dark:text-red-300 font-medium leading-relaxed">
                {{ __('sales.confirm_cancel_desc') }}
            </p>
        </div>

        @if($selectedSale)
            <div class="bg-gray-50/80 dark:bg-gray-800/80 rounded-xl p-4 space-y-2.5 border border-gray-100 dark:border-gray-700/80">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('sales.customer') }}:</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $selectedSale->customer->name ?? __('sales.customer_placeholder') }}</span>
                </div>
                <div class="flex justify-between text-sm items-baseline">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('sales.total') }}:</span>
                    <span class="font-bold text-gray-900 dark:text-white tabular-nums text-base">R$ {{ number_format($selectedSale->total_amount, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm pt-2.5 border-t border-gray-200/80 dark:border-gray-700/80">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('sales.items_to_restore') }}:</span>
                    <span class="font-semibold text-gray-900 dark:text-white tabular-nums">{{ $selectedSale->items->sum('quantity') }}</span>
                </div>
            </div>
        @endif
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-3">
            <x-button flat label="{{ __('sales.cancel') }}" x-on:click="close" />
            <x-button negative icon="x-circle" label="{{ __('sales.yes_cancel_sale') }}" wire:click="cancelSale" spinner="cancelSale" class="font-semibold shadow-xs" />
        </div>
    </x-slot>
</x-modal-card>
