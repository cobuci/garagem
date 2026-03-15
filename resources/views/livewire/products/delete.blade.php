<div>
    <x-modal-card title="{{ __('products.delete') }}" wire:model.defer="deleteModal" spacing="p-4" align="center" focus="false" persistent>
        <div class="space-y-4">
            @if($product)
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('products.delete_confirm', ['product' => $product->name]) }}
                </p>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    {{ __('products.delete_instruction', ['word' => __('products.delete_word')]) }}
                </p>
                <x-input
                    wire:model.defer="confirmation"
                    placeholder="{{ __('products.delete_word') }}"
                    autocomplete="off"
                    x-on:keydown.enter="$wire.destroy()"
                />
            @endif
        </div>

        <x-slot name="footer" class="flex justify-end gap-x-4">
            <x-button flat label="{{ __('products.cancel') }}" x-on:click="$wire.deleteModal = false" />
            <x-button negative label="{{ __('products.delete') }}" wire:click="destroy" />
        </x-slot>
    </x-modal-card>
</div>
