<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('settings.title') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('settings.subtitle') }}</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('settings.sections.preferences.title') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('settings.sections.preferences.description') }}</p>
            </div>
            <div class="p-6">
                <div class="max-w-xs">
                    <x-select
                        label="{{ __('settings.sections.preferences.language') }}"
                        wire:model="form.locale"
                        :options="[
                            ['name' => 'Português (Brasil)', 'id' => 'pt_BR'],
                            ['name' => 'English', 'id' => 'en'],
                        ]"
                        option-label="name"
                        option-value="id"
                        :clearable="false"
                    />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('settings.sections.general.title') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('settings.sections.general.description') }}</p>
            </div>
            <div class="p-6">
                <x-input
                    wire:model="form.store_name"
                    label="{{ __('settings.sections.general.store_name') }}"
                    placeholder="{{ __('settings.sections.general.store_name_placeholder') }}"
                />
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('settings.sections.fees.title') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('settings.sections.fees.description') }}</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-number
                    wire:model="form.credit_card_fee"
                    label="{{ __('settings.sections.fees.credit_fee') }}"
                    placeholder="0,00"
                    step="0.01"
                    suffix="%"
                />
                <x-number
                    wire:model="form.debit_card_fee"
                    label="{{ __('settings.sections.fees.debit_fee') }}"
                    placeholder="0,00"
                    step="0.01"
                    suffix="%"
                />
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('settings.sections.address.title') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('settings.sections.address.description') }}</p>
            </div>
            <div class="p-6 space-y-6">
                <x-input
                    wire:model="form.address"
                    label="{{ __('settings.sections.address.street') }}"
                    placeholder="{{ __('settings.sections.address.street_placeholder') }}"
                />
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-input
                        wire:model="form.city"
                        label="{{ __('settings.sections.address.city') }}"
                    />
                    <x-input
                        wire:model="form.state"
                        label="{{ __('settings.sections.address.state') }}"
                    />
                    <x-input
                        wire:model="form.zip_code"
                        label="{{ __('settings.sections.address.zip_code') }}"
                        placeholder="{{ __('settings.sections.address.zip_code_placeholder') }}"
                    />
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <x-button
                type="submit"
                primary
                lg
                class="w-full md:w-auto"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>{{ __('settings.actions.save') }}</span>
                <span wire:loading>{{ __('settings.actions.saving') }}</span>
            </x-button>
        </div>
    </form>
</div>
