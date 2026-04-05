<div class="flex flex-col h-full">
    <div class="flex-1 overflow-y-auto space-y-5 pb-4">
        {{-- Changelog fields --}}
        <div class="grid grid-cols-2 gap-4">
            <x-input
                wire:model="version"
                label="{{ __('changelog.admin.version') }}"
                placeholder="1.0.0"
            />
            <x-input
                wire:model="releasedAt"
                type="date"
                label="{{ __('changelog.admin.released_at') }}"
            />
        </div>

        <x-input
            wire:model="title"
            label="{{ __('changelog.admin.title_field') }}"
            placeholder="{{ __('changelog.admin.title_field') }}"
        />

        {{-- Items --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                    {{ __('changelog.admin.items') }}
                </h3>
            </div>

            <div class="space-y-4">
                @foreach($items as $index => $item)
                    <div wire:key="item-{{ $index }}" class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-900/30">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                #{{ $index + 1 }}
                            </span>
                            @if(count($items) > 1)
                                <button wire:click="removeItem({{ $index }})"
                                        class="p-1 text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <x-input
                                wire:model="items.{{ $index }}.title"
                                label="{{ __('changelog.admin.item_title') }}"
                                placeholder="{{ __('changelog.admin.item_title') }}"
                            />

                            <x-textarea
                                wire:model="items.{{ $index }}.description"
                                label="{{ __('changelog.admin.item_description') }}"
                                rows="3"
                            />

                            {{-- Image upload --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('changelog.admin.item_image') }}
                                </label>

                                @if($item['image_path'] && empty($item['upload']))
                                    <div class="mb-2 relative inline-block">
                                        <img src="{{ Storage::url($item['image_path']) }}"
                                             class="h-24 w-auto rounded-lg object-cover border border-gray-200 dark:border-gray-700" />
                                    </div>
                                @endif

                                @if(!empty($item['upload']))
                                    <div class="mb-2">
                                        <img src="{{ $item['upload']->temporaryUrl() }}"
                                             class="h-24 w-auto rounded-lg object-cover border border-indigo-300" />
                                    </div>
                                @endif

                                <input type="file"
                                       wire:model="items.{{ $index }}.upload"
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                                              file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                              file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700
                                              hover:file:bg-indigo-100 dark:file:bg-indigo-900/40 dark:file:text-indigo-300
                                              cursor-pointer" />
                                <x-error field="items.{{ $index }}.upload" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button wire:click="addItem"
                    class="mt-3 w-full flex items-center justify-center gap-2 py-2 px-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-500 dark:text-gray-400 hover:border-indigo-400 hover:text-indigo-600 dark:hover:border-indigo-500 dark:hover:text-indigo-400 transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                {{ __('changelog.admin.add_item') }}
            </button>
        </div>
    </div>

    <div class="flex justify-end gap-x-4 pt-6 border-t border-gray-100 dark:border-gray-700 shrink-0">
        <x-button flat label="{{ __('changelog.admin.cancel') }}" x-on:click="$wire.$parent.showForm = false" />
        <x-button primary label="{{ __('changelog.admin.save') }}" wire:click="save" />
    </div>
</div>
