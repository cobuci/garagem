<div>
    <x-drawer wire:model="showDrawer" :title="__('customers.new_customer')">
        <form wire:submit="save" class="space-y-4">
            <x-input wire:model="form.name" :label="__('customers.name')" />
            <x-input wire:model="form.email" :label="__('customers.email')" type="email" />
            <x-input wire:model="form.phone" :label="__('customers.phone')" />

            <div class="grid grid-cols-2 gap-4">
                <x-select
                    wire:model="form.gender"
                    :label="__('customers.gender')"
                    :options="[
                        ['name' => __('customers.male'),  'id' => \App\Enums\Gender::Male->value],
                        ['name' => __('customers.female'), 'id' => \App\Enums\Gender::Female->value],
                    ]"
                    option-label="name"
                    option-value="id"
                />
                <x-input wire:model="form.zip_code" :label="__('customers.zip_code')" />
            </div>

            <x-input wire:model="form.street" :label="__('customers.street')" />
            <x-input wire:model="form.neighborhood" :label="__('customers.neighborhood')" />
            <x-input wire:model="form.address" :label="__('customers.address')" />

            <div class="flex justify-end gap-x-3 pt-6 border-t border-gray-100 dark:border-gray-700 mt-6">
                <x-button flat :label="__('customers.cancel')" x-on:click="$wire.showDrawer = false" />
                <x-button primary :label="__('customers.save')" type="submit" />
            </div>
        </form>
    </x-drawer>
</div>
