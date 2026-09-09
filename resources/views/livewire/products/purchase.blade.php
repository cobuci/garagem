<div class="w-full sm:w-auto">
    <x-button
        outline
        icon="arrow-down-tray"
        label="{{ __('products.add_stock') }}"
        class="w-full sm:w-auto justify-center bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white font-semibold shadow-xs transition-all duration-150 active:scale-[0.98]"
        x-on:click="$dispatch('purchase:open')"
    />

    <x-drawer wire:model.live="purchaseDrawer" title="{{ __('products.purchase_title') }}" right md>
        <div class="flex flex-col h-full">
            <div class="flex-1 overflow-y-auto space-y-4 pr-1">
                <div class="grid grid-cols-1 gap-4">
                    <x-select
                        label="{{ __('products.category') }}"
                        placeholder="{{ __('products.select_category') }}"
                        wire:model.live="form.categoryId"
                        :options="$this->categories"
                        option-label="name"
                        option-value="id"
                    />

                    <x-select
                        label="{{ __('products.product') }}"
                        placeholder="{{ __('products.select_product') }}"
                        wire:model.live="form.productId"
                        :options="$this->products"
                        option-label="label"
                        option-value="id"
                        :disabled="!$this->form->categoryId"
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <x-number
                            label="{{ __('products.quantity') }}"
                            placeholder="0"
                            wire:model.live="form.quantity"
                            min="1"
                        />

                        <x-datetime-picker
                            label="{{ __('products.expiration_date') }}"
                            placeholder="{{ __('products.expiration_date') }}"
                            wire:model="form.expirationDate"
                            without-time
                            clearable
                        />
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-800 space-y-4 shadow-sm">
                        <h3 class="font-semibold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                            <x-icon name="presentation-chart-line" class="w-4 h-4 text-gray-400" />
                            {{ __('products.cost_values') }}
                        </h3>

                        <div class="grid grid-cols-2 gap-4">
                            <x-money-input
                                label="{{ __('products.unit_cost') }}"
                                wire:model.live.debounce.500ms="form.unitCost"
                                prefix="{{ __('products.currency_symbol') }}"
                            />

                            <x-money-input
                                label="{{ __('products.total_cost') }}"
                                wire:model.live.debounce.500ms="form.totalCost"
                                prefix="{{ __('products.currency_symbol') }}"
                            />
                        </div>
                    </div>

                    <div class="p-4 bg-sky-50/50 dark:bg-sky-950/20 rounded-xl space-y-4 border border-sky-100 dark:border-sky-900/40 shadow-sm">
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <x-money-input
                                label="{{ __('products.sale_price') }}"
                                wire:model.live.debounce.500ms="form.salePrice"
                                prefix="{{ __('products.currency_symbol') }}"
                            />

                            <div class="flex flex-col justify-end text-right">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('products.estimated_profit') }}</span>
                                <span class="text-lg font-bold tabular-nums {{ $this->profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ __('products.currency_symbol') }} {{ number_format($this->profit, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 border-t pt-4 border-gray-100 dark:border-gray-800">
                        <x-datetime-picker
                            label="{{ __('products.invoice_date') }}"
                            placeholder="{{ __('products.invoice_date') }}"
                            wire:model="form.invoiceDate"
                            without-time
                            clearable
                        />

                        <div class="grid grid-cols-2 gap-4">
                            <x-datetime-picker
                                label="{{ __('products.payment_date') }}"
                                placeholder="{{ __('products.payment_date') }}"
                                wire:model="form.paymentDate"
                                without-time
                                clearable
                            />

                            <x-datetime-picker
                                label="{{ __('products.due_date') }}"
                                placeholder="{{ __('products.due_date') }}"
                                wire:model="form.dueDate"
                                without-time
                                clearable
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-x-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4 shrink-0">
                <x-button flat label="{{ __('products.cancel') }}" wire:click="closeDrawer" />
                <x-button primary icon="check" label="{{ __('products.save') }}" class="font-semibold shadow-xs hover:shadow transition-all duration-150 active:scale-[0.98] min-w-24" wire:click="save" spinner="save" />
            </div>
        </div>
    </x-drawer>
</div>
