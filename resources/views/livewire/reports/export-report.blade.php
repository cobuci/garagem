<div>
    <x-button
        secondary
        outline
        icon="arrow-down-tray"
        :label="__('reports.export.trigger')"
        x-on:click="$wire.set('showModal', true)"
    />

    <x-modal-card wire:model.defer="showModal" :title="__('reports.export.title')" align="center" max-width="lg">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            {{ __('reports.export.subtitle') }}
        </p>

        <div class="mb-5 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">
                {{ __('reports.export.quick_select') }}
            </p>
            <div class="grid grid-cols-4 gap-2">
                @foreach([
                    'today'        => __('reports.periods.today'),
                    'yesterday'    => __('reports.periods.yesterday'),
                    'last_7_days'  => __('reports.periods.last_7_days'),
                    'last_30_days' => __('reports.periods.last_30_days'),
                    'this_month'   => __('reports.periods.this_month'),
                    'last_month'   => __('reports.periods.last_month'),
                    'this_year'    => __('reports.periods.this_year'),
                    'last_year'    => __('reports.periods.last_year'),
                ] as $key => $label)
                    <button
                        type="button"
                        wire:click="applyPeriod('{{ $key }}')"
                        class="py-1.5 px-2 text-xs font-medium rounded-md text-center border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-primary-50 hover:border-primary-400 hover:text-primary-700 dark:hover:bg-primary-900/30 dark:hover:border-primary-500 dark:hover:text-primary-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-1 dark:focus-visible:ring-offset-gray-800 active:scale-95 transition-all cursor-pointer"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

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
                <x-button flat :label="__('reports.export.cancel')" x-on:click="close" />
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
