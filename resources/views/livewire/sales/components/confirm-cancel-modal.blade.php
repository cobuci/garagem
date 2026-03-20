@props(['selectedSale'])

<x-modal-card wire:model="showConfirmCancelModal" title="{{ __('sales.confirm_cancel') }}" max-width="md">
    <div class="space-y-4">
        <p class="text-red-600 dark:text-red-400 font-medium">
            {{ __('sales.confirm_cancel_desc') }}
        </p>

        @if($selectedSale)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 space-y-2 border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ __('sales.customer') }}:</span>
                    <span class="font-bold text-gray-700 dark:text-gray-200">{{ $selectedSale->customer->name ?? __('sales.customer_placeholder') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ __('sales.total') }}:</span>
                    <span class="font-bold text-gray-700 dark:text-gray-200">R$ {{ number_format($selectedSale->total_amount, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-gray-500">{{ __('sales.items_to_restore') }}:</span>
                    <span class="font-bold text-gray-700 dark:text-gray-200">{{ $selectedSale->items->sum('quantity') }}</span>
                </div>
            </div>
        @endif
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
            <x-button flat label="{{ __('sales.cancel') }}" x-on:click="close" />
            <x-button negative label="{{ __('sales.yes_cancel_sale') }}" wire:click="cancelSale" spinner="cancelSale" />
        </div>
    </x-slot>
</x-modal-card>
