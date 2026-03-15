<div>
    <x-button
        primary
        icon="plus"
        label="{{ __('products.new') }}"
        x-on:click="$wire.createDrawer = true"
    />

    <x-drawer wire:model.defer="createDrawer" title="{{ __('products.create') }}" right md>
        <div class="flex flex-col h-full">
            <div class="flex-1 overflow-y-auto">
                <div class="grid grid-cols-1 gap-4">
                    <x-native-select
                        label="{{ __('products.category') }}"
                        placeholder="{{ __('products.category') }}"
                        wire:model.defer="form.categoryId"
                    >
                        <option value="">{{ __('products.category') }}</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </x-native-select>

                    <x-input label="{{ __('products.name') }}" placeholder="{{ __('products.name') }}" wire:model.defer="form.name" />

                    <x-input label="{{ __('products.brand') }}" placeholder="{{ __('products.brand') }}" wire:model.defer="form.brand" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-input
                            type="number"
                            step="0.01"
                            label="{{ __('products.weight') }}"
                            placeholder="0.00"
                            wire:model.defer="form.weightValue"
                        />

                        <x-native-select
                            label="{{ __('products.weight_type') }}"
                            placeholder="{{ __('products.weight_type') }}"
                            wire:model.defer="form.weightType"
                        >
                            <option value="ml">{{ __('products.ml') }}</option>
                            <option value="l">{{ __('products.l') }}</option>
                            <option value="g">{{ __('products.g') }}</option>
                            <option value="kg">{{ __('products.kg') }}</option>
                            <option value="unit">{{ __('products.unit') }}</option>
                        </x-native-select>
                    </div>

                    <x-input label="{{ __('products.upc') }}" placeholder="{{ __('products.upc') }}" wire:model.defer="form.upc" />
                </div>
            </div>

            <div class="flex justify-end gap-x-4 pt-6 border-t border-gray-100 dark:border-gray-700 mt-6 shrink-0">
                <x-button flat label="{{ __('products.cancel') }}" x-on:click="$wire.createDrawer = false" />
                <x-button primary label="{{ __('products.save') }}" wire:click="create" />
            </div>
        </div>
    </x-drawer>
</div>
