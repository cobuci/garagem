<x-modal-card wire:model="showConfirmCancelModal" title="{{ __('finance.cancel_adjustment_title') }}" max-width="md">
    <div class="space-y-4">
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('finance.cancel_adjustment_description') }}
        </p>
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
            <x-button flat label="{{ __('finance.adjustment_modal.cancel') }}" x-on:click="close" />
            <x-button negative label="{{ __('finance.adjustment_modal.confirm') }}" wire:click="cancelAdjustment" />
        </div>
    </x-slot>
</x-modal-card>
