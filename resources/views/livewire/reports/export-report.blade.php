<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 h-full flex flex-col justify-center">
    <div class="flex items-center gap-4 mb-4">
        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
            <x-icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.export.title') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('reports.export.subtitle') }}</p>
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

    <x-button
        primary
        lg
        full
        wire:click="export"
        wire:loading.attr="disabled"
        spinner="export"
    >
        <div class="flex items-center gap-2">
            <x-icon name="paper-airplane" class="w-5 h-5" />
            {{ __('reports.export.button') }}
        </div>
    </x-button>
</div>
