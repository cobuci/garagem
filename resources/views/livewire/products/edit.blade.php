<div>
    <x-drawer wire:model="editDrawer" title="{{ __('products.edit') }}" right md>
        <div class="flex flex-col h-full">
            <div class="flex-1 overflow-y-auto space-y-4 pr-1">
                <div class="grid grid-cols-1 gap-4">
                    <x-select
                        label="{{ __('products.category') }}"
                        placeholder="{{ __('products.select_category') }}"
                        wire:model="form.categoryId"
                        :options="$this->categories"
                        option-label="name"
                        option-value="id"
                    />

                    <x-input label="{{ __('products.name') }}" placeholder="{{ __('products.name') }}" wire:model="form.name" />

                    <x-input label="{{ __('products.brand') }}" placeholder="{{ __('products.brand') }}" wire:model="form.brand" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-number
                            label="{{ __('products.weight') }}"
                            placeholder="0"
                            wire:model="form.weightValue"
                            step="1"
                        />

                        <x-select
                            label="{{ __('products.weight_type') }}"
                            placeholder="{{ __('products.weight_type') }}"
                            wire:model="form.weightType"
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

                    <x-input label="{{ __('products.upc') }}" placeholder="{{ __('products.upc') }}" wire:model="form.upc" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-money-input
                            label="{{ __('products.cost') }}"
                            placeholder="0,00"
                            prefix="{{ __('products.currency_symbol') }}"
                            wire:model="form.unitCost"
                        />

                        <x-money-input
                            label="{{ __('products.sale') }}"
                            placeholder="0,00"
                            prefix="{{ __('products.currency_symbol') }}"
                            wire:model="form.salePrice"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-input
                            type="number"
                            label="{{ __('products.stock') }}"
                            placeholder="0"
                            wire:model="form.stockQuantity"
                        />

                        <x-datetime-picker
                            label="{{ __('products.expiration_date') }}"
                            placeholder="{{ __('products.expiration_date') }}"
                            without-timezone
                            without-time
                            wire:model="form.expirationDate"
                        />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-x-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4 shrink-0">
                <x-button flat label="{{ __('products.cancel') }}" x-on:click="$wire.editDrawer = false" />
                <x-button primary icon="check" label="{{ __('products.save') }}" class="font-semibold shadow-xs hover:shadow transition-all duration-150 active:scale-[0.98] min-w-24" wire:click="update" spinner="update" />
            </div>
        </div>
    </x-drawer>
</div>
