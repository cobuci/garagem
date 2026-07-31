<div>
    <x-modal-card
        :title="__('categories.actions.delete')"
        wire:model="deleteModal"
        spacing="p-4"
        align="center"
        focus="false"
        persistent
    >
        <div class="space-y-4">
            @if ($category)
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('categories.messages.delete_confirm', ['category' => $category->name]) }}
                </p>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    {{ __('categories.messages.delete_instruction', ['word' => __('categories.delete_word')]) }}
                </p>
                <x-input
                    wire:model="confirmation"
                    :placeholder="__('categories.delete_word')"
                    autocomplete="off"
                    wire:keydown.enter="destroy"
                />
            @endif
        </div>

        <x-slot name="footer" class="flex justify-end gap-x-4">
            <x-button flat :label="__('categories.actions.cancel')" wire:click="$set('deleteModal', false)" />
            <x-button negative :label="__('categories.actions.delete')" wire:click="destroy" />
        </x-slot>
    </x-modal-card>
</div>
