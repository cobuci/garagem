@props(['totalPendingAmount'])

<div class="flex-none bg-gray-50 dark:bg-gray-900 pb-2 w-full max-w-full">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('sales.history_title') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('sales.history_subtitle') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('sales.total_pending') }}</p>
            <p class="text-lg font-bold text-red-600 dark:text-red-400">
                R$ {{ number_format($totalPendingAmount, 2, ',', '.') }}
            </p>
        </div>
    </div>
</div>
