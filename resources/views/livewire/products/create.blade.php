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
                    <x-select
                        label="{{ __('products.category') }}"
                        placeholder="{{ __('products.category') }}"
                        wire:model.defer="form.categoryId"
                        :options="$this->categories"
                        option-label="name"
                        option-value="id"
                    />

                    <x-input label="{{ __('products.name') }}" placeholder="{{ __('products.name') }}" wire:model.defer="form.name" />

                    <x-input label="{{ __('products.brand') }}" placeholder="{{ __('products.brand') }}" wire:model.defer="form.brand" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-number
                            label="{{ __('products.weight') }}"
                            placeholder="0"
                            wire:model.defer="form.weightValue"
                            step="1"
                        />

                        <x-select
                            label="{{ __('products.weight_type') }}"
                            placeholder="{{ __('products.weight_type') }}"
                            wire:model.defer="form.weightType"
                            :options="[
                                ['name' => __('products.ml'), 'id' => 'ml'],
                                ['name' => __('products.l'), 'id' => 'l'],
                                ['name' => __('products.g'), 'id' => 'g'],
                                ['name' => __('products.kg'), 'id' => 'kg'],
                                ['name' => __('products.unit'), 'id' => 'unit'],
                            ]"
                            option-label="name"
                            option-value="id"
                        />
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
