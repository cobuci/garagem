<x-modal-card wire:model="showMarkAllAsPaidModal" title="{{ __('customers.mark_all_as_paid_confirm') }}" max-width="md">
    <div class="space-y-4">
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('customers.mark_all_as_paid_desc') }}
        </p>

        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 space-y-2 border border-gray-100 dark:border-gray-700">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">{{ __('customers.total_due') }}:</span>
                <span class="font-bold text-red-600">R$ {{ number_format($this->totalDue, 2, ',', '.') }}</span>
            </div>
        </div>

        <div class="pt-2">
            <x-checkbox
                id="confirmedMarkAllAsPaid"
                wire:model.live="confirmedMarkAllAsPaid"
                label="{{ __('customers.mark_all_as_paid_checkbox') }}"
            />
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
            <x-button flat label="{{ __('customers.cancel') }}" x-on:click="close" />
            <x-button
                primary
                label="{{ __('customers.mark_all_as_paid') }}"
                wire:click="markAllAsPaid"
                spinner="markAllAsPaid"
                :disabled="!$confirmedMarkAllAsPaid"
            />
        </div>
    </x-slot>
</x-modal-card>
