<x-modal-card name="confirmPaymentModal" title="{{ __('bills_payable.actions.confirm_modal.title') }}" max-width="md" align="center">
    @if($selectedBill)
        <div class="space-y-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('bills_payable.actions.confirm_modal.description') }}
            </p>

            <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700 space-y-2">
                <div class="flex justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('bills_payable.actions.confirm_modal.product') }}:</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $selectedBill->product->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('bills_payable.actions.confirm_modal.due_date') }}:</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $selectedBill->due_date?->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('bills_payable.actions.confirm_modal.value') }}:</span>
                    <span class="text-lg font-black text-primary-600">R$ {{ number_format($selectedBill->total_cost, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <x-slot name="footer" class="flex justify-end gap-x-3">
        <x-button flat label="{{ __('products.cancel') }}" x-on:click="$closeModal('confirmPaymentModal')" />
        <x-button primary label="{{ __('bills_payable.actions.confirm_modal.confirm') }}" wire:click="markAsPaid" />
    </x-slot>
</x-modal-card>
