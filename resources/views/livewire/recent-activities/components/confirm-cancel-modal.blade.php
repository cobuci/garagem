<x-modal-card wire:model="showConfirmCancelModal" title="{{ __('finance.cancel_adjustment_title') }}" max-width="md">
    <div class="space-y-4">
        <div class="p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200/80 dark:border-red-800/60 flex items-start gap-3">
            <div class="p-1 rounded-lg bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 shrink-0">
                <x-icon name="exclamation-triangle" class="w-4 h-4" />
            </div>
            <div class="text-xs text-red-700 dark:text-red-300 font-medium leading-relaxed">
                {{ __('finance.cancel_adjustment_description') }}
            </div>
        </div>

        @if($this->cancellingTransaction)
            <div class="bg-gray-50/80 dark:bg-gray-800/80 rounded-xl p-4 space-y-3 border border-gray-200/80 dark:border-gray-700/80">
                <div class="flex justify-between items-start text-xs">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('finance.table.description') }}:</span>
                    <span class="font-semibold text-gray-900 dark:text-white text-right max-w-[200px] truncate">
                        {{ $this->cancellingTransaction->description }}
                    </span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('finance.table.date') }}:</span>
                    <span class="font-medium text-gray-700 dark:text-gray-300 tabular-nums">
                        {{ $this->cancellingTransaction->transaction_date->format('d/m/Y H:i') }}
                    </span>
                </div>
                <div class="flex justify-between items-baseline pt-2.5 border-t border-gray-200/80 dark:border-gray-700/80">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('finance.table.amount') }}:</span>
                    <span @class([
                        'text-base font-bold tabular-nums',
                        'text-emerald-600 dark:text-emerald-400' => $this->cancellingTransaction->amount > 0,
                        'text-red-600 dark:text-red-400' => $this->cancellingTransaction->amount < 0,
                    ])>
                        {{ $this->cancellingTransaction->amount > 0 ? '+' : '' }} R$ {{ number_format(abs($this->cancellingTransaction->amount) / 100, 2, ',', '.') }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    <x-slot name="footer">
        <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-2 sm:gap-3 w-full">
            <x-button flat label="{{ __('finance.adjustment_modal.cancel') }}" x-on:click="close" class="w-full sm:w-auto justify-center" />
            <x-button
                negative
                icon="x-circle"
                label="{{ __('finance.adjustment_modal.confirm') }}"
                wire:click="cancelAdjustment"
                spinner="cancelAdjustment"
                class="font-semibold shadow-xs w-full sm:w-auto justify-center"
            />
        </div>
    </x-slot>
</x-modal-card>
