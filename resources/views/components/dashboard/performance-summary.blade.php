@props(['chartData'])

<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-center">
    <div class="text-center mb-8">
        <div class="inline-flex p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl mb-4">
            <x-icon name="presentation-chart-line" class="w-10 h-10 text-indigo-600 dark:text-indigo-400" />
        </div>
        <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('dashboard.performance_summary') }}</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('dashboard.summary_text') }}</p>
    </div>

    <div class="space-y-6">
        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('dashboard.avg_monthly_sales') }}</span>
                <x-icon name="chart-bar" class="w-4 h-4 text-indigo-500" />
            </div>
            <p class="text-lg font-bold text-gray-900 dark:text-white">R$ {{ number_format(collect($chartData['sales'])->avg(), 2, ',', '.') }}</p>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('dashboard.avg_monthly_profit') }}</span>
                <x-icon name="currency-dollar" class="w-4 h-4 text-emerald-500" />
            </div>
            <p class="text-lg font-bold text-gray-900 dark:text-white">R$ {{ number_format(collect($chartData['profit'])->avg(), 2, ',', '.') }}</p>
        </div>
    </div>
</div>
