<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <div class="flex-none bg-gray-50 dark:bg-gray-900 pb-2 w-full max-w-full">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('reports.title') }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('reports.subtitle') }}</p>
            </div>
        </div>
    </div>

    <div class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 flex flex-col items-center justify-center min-h-[400px]">
        <div class="bg-primary-50 dark:bg-primary-900/20 p-4 rounded-full mb-4">
            <x-icon name="chart-bar" class="w-12 h-12 text-primary-600 dark:text-primary-400" />
        </div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('reports.title') }}</h2>
        <p class="text-gray-500 dark:text-gray-400 text-center max-w-md">
            {{ __('reports.subtitle') }}
        </p>
    </div>
</div>
