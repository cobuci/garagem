<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('settings.import_legacy_data') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('settings.import_legacy_description') }}
            </p>
        </div>
    </div>

    <x-card>
        <form wire:submit="save" class="space-y-4">
            <x-input
                wire:model="file"
                type="file"
                label="{{ __('settings.sql_file') }}"
                hint="{{ __('settings.sql_file_hint') }}"
                accept=".sql"
            />

            <div class="flex justify-end">
                <x-button
                    type="submit"
                    primary
                    label="{{ __('settings.import_now') }}"
                    spinner="save"
                />
            </div>
        </form>
    </x-card>

    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-icon name="information-circle" class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    {{ __('settings.import_notice') }}
                </p>
            </div>
        </div>
    </div>
</div>
