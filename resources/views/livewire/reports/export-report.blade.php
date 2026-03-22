<div>
    <x-button
        secondary
        outline
        icon="chart-bar"
        label="Export"
        x-on:click="$wire.set('showModal', true)"
    />

    <x-modal-card wire:model.defer="showModal" :title="__('reports.export.title')" align="center" max-width="lg">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            {{ __('reports.export.subtitle') }}
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <x-datetime-picker
                wire:model="startDate"
                :label="__('reports.export.start_date')"
                :without-time="true"
                display-format="DD/MM/YYYY"
            />
            <x-datetime-picker
                wire:model="endDate"
                :label="__('reports.export.end_date')"
                :without-time="true"
                display-format="DD/MM/YYYY"
            />
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button
                    primary
                    wire:click="export"
                    wire:loading.attr="disabled"
                    spinner="export"
                    icon="paper-airplane"
                    :label="__('reports.export.button')"
                />
            </div>
        </x-slot>
    </x-modal-card>
</div>
