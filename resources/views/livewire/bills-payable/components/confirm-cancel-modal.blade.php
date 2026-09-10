<x-modal-card name="confirmCancellationModal" title="{{ __('bills_payable.actions.cancel_modal.title') }}" max-width="md" align="center">
    @if($selectedBill)
        <div class="space-y-4">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-rose-50/70 dark:bg-rose-950/40 border border-rose-100 dark:border-rose-900/40">
                <div class="w-9 h-9 rounded-lg bg-rose-100 dark:bg-rose-900/60 flex items-center justify-center text-rose-600 dark:text-rose-300 shrink-0">
                    <x-icon name="exclamation-triangle" class="w-5 h-5" />
                </div>
                <p class="text-xs text-rose-900 dark:text-rose-200 leading-relaxed">
                    {{ __('bills_payable.actions.cancel_modal.description') }}
                </p>
            </div>

            <div class="bg-gray-50/80 dark:bg-gray-900/60 p-4 rounded-xl border border-gray-200/80 dark:border-gray-700/80 space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('bills_payable.actions.confirm_modal.product') }}</span>
                    <div class="flex items-center gap-1.5 text-right">
                        <span class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $selectedBill->product->name }}</span>
                        @if($selectedBill->product->weight)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-2xs font-bold bg-gray-200/80 dark:bg-gray-700 text-gray-700 dark:text-gray-200 tabular-nums shrink-0">
                                {{ $selectedBill->product->weight }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('bills_payable.table.quantity') }}</span>
                    <span class="text-sm font-semibold text-rose-600 dark:text-rose-400 tabular-nums">-{{ $selectedBill->quantity }} un. no estoque</span>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('bills_payable.actions.confirm_modal.value') }}</span>
                    <span class="text-lg font-black text-rose-600 dark:text-rose-400 tabular-nums">R$ {{ number_format($selectedBill->total_cost, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <x-slot name="footer" class="flex justify-end gap-x-3">
        <x-button flat label="{{ __('products.cancel') }}" x-on:click="$closeModal('confirmCancellationModal')" />
        <x-button
            negative
            icon="trash"
            label="{{ __('bills_payable.actions.cancel_modal.confirm') }}"
            wire:click="cancelBill"
            wire:loading.attr="disabled"
            wire:target="cancelBill"
            class="font-semibold shadow-xs"
        />
    </x-slot>
</x-modal-card>
