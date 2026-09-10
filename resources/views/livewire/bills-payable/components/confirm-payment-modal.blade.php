<x-modal-card name="confirmPaymentModal" title="{{ __('bills_payable.actions.confirm_modal.title') }}" max-width="md" align="center">
    @if($selectedBill)
        <div class="space-y-4">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/40">
                <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center text-emerald-600 dark:text-emerald-300 shrink-0">
                    <x-icon name="banknotes" class="w-5 h-5" />
                </div>
                <p class="text-xs text-emerald-900 dark:text-emerald-200 leading-relaxed">
                    {{ __('bills_payable.actions.confirm_modal.description') }}
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

                @if($selectedBill->product->brand)
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Marca / Fornecedor</span>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ $selectedBill->product->brand }}</span>
                    </div>
                @endif

                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('bills_payable.table.quantity') }}</span>
                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 tabular-nums">{{ $selectedBill->quantity }} un.</span>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('bills_payable.actions.confirm_modal.due_date') }}</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">{{ $selectedBill->due_date?->format('d/m/Y') }}</span>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('bills_payable.actions.confirm_modal.value') }}</span>
                    <span class="text-xl font-black text-primary-600 dark:text-primary-400 tabular-nums">R$ {{ number_format($selectedBill->total_cost, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <x-slot name="footer" class="flex justify-end gap-x-3">
        <x-button flat label="{{ __('products.cancel') }}" x-on:click="$closeModal('confirmPaymentModal')" />
        <x-button
            primary
            icon="check"
            label="{{ __('bills_payable.actions.confirm_modal.confirm') }}"
            wire:click="markAsPaid"
            wire:loading.attr="disabled"
            wire:target="markAsPaid"
            class="font-semibold shadow-xs"
        />
    </x-slot>
</x-modal-card>
