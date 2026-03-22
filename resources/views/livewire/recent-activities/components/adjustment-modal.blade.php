<x-modal-card name="adjustmentModal" wire:model="showAdjustmentModal" title="{{ $adjustmentType === 'add' ? __('finance.adjustment_modal.title_add') : __('finance.adjustment_modal.title_remove') }}">
    <div class="grid grid-cols-1 gap-4">
        <x-money-input
            label="{{ __('finance.adjustment_modal.amount') }}"
            placeholder="0,00"
            wire:model="amount"
            prefix="R$"
        />
        <x-textarea label="{{ __('finance.adjustment_modal.description') }}" wire:model="description" />
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
            <x-button flat label="{{ __('finance.adjustment_modal.cancel') }}" x-on:click="close" />
            <x-button primary label="{{ __('finance.adjustment_modal.confirm') }}" wire:click="saveAdjustment" />
        </div>
    </x-slot>
</x-modal-card>
