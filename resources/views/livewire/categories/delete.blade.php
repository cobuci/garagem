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
                    {!! __('categories.messages.delete_confirm', ['category' => '<span class="font-semibold text-gray-900 dark:text-white">' . e($category->name) . '</span>']) !!}
                </p>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    {!! __('categories.messages.delete_instruction', ['word' => '<span class="font-mono font-semibold px-1.5 py-0.5 rounded bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-400 text-xs select-all">' . __('categories.delete_word') . '</span>']) !!}
                </p>
                <x-input
                    wire:model="confirmation"
                    :placeholder="__('categories.delete_word')"
                    autocomplete="off"
                    x-on:keydown.enter="$wire.destroy()"
                />
            @endif
        </div>

        <x-slot name="footer" class="flex justify-end gap-x-3">
            <x-button flat :label="__('categories.actions.cancel')" x-on:click="$wire.deleteModal = false" />
            <x-button
                negative
                icon="trash"
                :label="__('categories.actions.delete')"
                class="font-semibold shadow-xs hover:shadow transition-all duration-150 active:scale-[0.98] min-w-24"
                wire:click="destroy"
                spinner="destroy"
            />
        </x-slot>
    </x-modal-card>
</div>
