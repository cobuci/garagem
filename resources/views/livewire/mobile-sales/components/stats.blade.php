@props(['totalSales', 'totalPending'])

<div class="flex-none w-full max-w-full">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('mobile_sales.title') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mobile_sales.subtitle') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mobile_sales.total_sales') }}</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalSales }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mobile_sales.total_pending') }}</p>
            <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400">{{ $totalPending }}</p>
        </div>
    </div>
</div>
