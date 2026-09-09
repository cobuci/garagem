<x-modal-card wire:model="showMarkAllAsPaidModal" :title="__('customers.mark_all_as_paid_confirm')" max-width="md">
    <div class="space-y-4">
        <div class="flex items-start space-x-3 p-4 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-200/70 dark:border-amber-800/40">
            <x-icon name="exclamation-triangle" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
            <p class="text-xs text-amber-800 dark:text-amber-300 font-medium leading-relaxed">
                {{ __('customers.mark_all_as_paid_desc') }}
            </p>
        </div>

        <div class="bg-gray-50 dark:bg-gray-900/60 rounded-xl p-4 border border-gray-200/80 dark:border-gray-700/80">
            <div class="flex items-center justify-between text-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('customers.total_due') }}:</span>
                <span class="text-base font-bold text-red-600 dark:text-red-400 tabular-nums">
                    R$ {{ number_format($this->totalDue, 2, ',', '.') }}
                </span>
            </div>
        </div>

        <div class="pt-1">
            <x-checkbox
                id="confirmedMarkAllAsPaid"
                wire:model.live="confirmedMarkAllAsPaid"
                :label="__('customers.mark_all_as_paid_checkbox')"
            />
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-3">
            <x-button flat :label="__('customers.cancel')" x-on:click="close" />
            <x-button
                primary
                :label="__('customers.mark_all_as_paid')"
                wire:click="markAllAsPaid"
                spinner="markAllAsPaid"
                :disabled="!$confirmedMarkAllAsPaid"
                class="font-medium shadow-xs"
            />
        </div>
    </x-slot>
</x-modal-card>
