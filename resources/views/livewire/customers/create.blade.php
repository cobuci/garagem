<div>
    <x-drawer wire:model="showDrawer" :title="__('customers.new_customer')">
        <form wire:submit="save" class="space-y-6">
            <div class="space-y-4">
                <div class="border-b border-gray-100 dark:border-gray-700/80 pb-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('customers.personal_info') }}</h4>
                </div>

                <x-input
                    wire:model="form.name"
                    :label="__('customers.name')"
                    icon="user"
                    placeholder="Nome completo da empresa ou cliente"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        wire:model="form.email"
                        :label="__('customers.email')"
                        type="email"
                        icon="envelope"
                        placeholder="contato@exemplo.com"
                    />
                    <x-input
                        wire:model="form.phone"
                        :label="__('customers.phone')"
                        icon="phone"
                        placeholder="(00) 00000-0000"
                    />
                </div>

                <x-select
                    wire:model="form.gender"
                    :label="__('customers.gender')"
                    :options="[
                        ['name' => __('customers.male'),  'id' => \App\Enums\Gender::Male->value],
                        ['name' => __('customers.female'), 'id' => \App\Enums\Gender::Female->value],
                    ]"
                    option-label="name"
                    option-value="id"
                    placeholder="Selecione o gênero (opcional)"
                />
            </div>

            <div class="space-y-4">
                <div class="border-b border-gray-100 dark:border-gray-700/80 pb-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('customers.address_info') }}</h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        wire:model="form.zip_code"
                        :label="__('customers.zip_code')"
                        icon="map-pin"
                        placeholder="00000-000"
                    />
                    <x-input
                        wire:model="form.neighborhood"
                        :label="__('customers.neighborhood')"
                        placeholder="Bairro"
                    />
                </div>

                <x-input
                    wire:model="form.street"
                    :label="__('customers.street')"
                    placeholder="Rua, Avenida, etc."
                />

                <x-input
                    wire:model="form.address"
                    :label="__('customers.address')"
                    placeholder="Número, apto, complemento"
                />
            </div>

            <div class="flex justify-end gap-x-3 pt-5 border-t border-gray-100 dark:border-gray-700/80 mt-6">
                <x-button
                    flat
                    :label="__('customers.cancel')"
                    x-on:click="$wire.showDrawer = false"
                />
                <x-button
                    primary
                    :label="__('customers.save')"
                    type="submit"
                    spinner="save"
                    class="font-medium shadow-xs"
                />
            </div>
        </form>
    </x-drawer>
</div>
