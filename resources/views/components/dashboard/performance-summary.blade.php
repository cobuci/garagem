@props(['chartData', 'monthlyMetrics'])

<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-center">
    <div class="flex items-center gap-3 mb-6">
        <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl text-indigo-600 dark:text-indigo-400 shrink-0">
            <x-icon name="presentation-chart-line" class="w-5 h-5" />
        </div>
        <div>
            <h4 class="text-base font-bold text-gray-900 dark:text-white leading-tight">{{ __('dashboard.performance_summary') }}</h4>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('dashboard.summary_text') }}</p>
        </div>
    </div>

    <div class="space-y-3">
        <div class="p-4 bg-gray-50/80 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('dashboard.avg_daily_sales') }}</span>
                <div class="p-1.5 bg-sky-50 dark:bg-sky-950/40 rounded-lg text-sky-600 dark:text-sky-400">
                    <x-icon name="calendar" class="w-4 h-4" />
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-white tracking-tight tabular-nums">R$ {{ number_format($monthlyMetrics['daily_average'] ?? 0, 2, ',', '.') }}</p>
        </div>

        <div class="p-4 bg-gray-50/80 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('dashboard.avg_monthly_sales') }}</span>
                <div class="p-1.5 bg-indigo-50 dark:bg-indigo-950/40 rounded-lg text-indigo-600 dark:text-indigo-400">
                    <x-icon name="chart-bar" class="w-4 h-4" />
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-white tracking-tight tabular-nums">R$ {{ number_format(collect($chartData['sales'])->avg(), 2, ',', '.') }}</p>
        </div>

        <div class="p-4 bg-gray-50/80 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('dashboard.avg_monthly_profit') }}</span>
                <div class="p-1.5 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg text-emerald-600 dark:text-emerald-400">
                    <x-icon name="currency-dollar" class="w-4 h-4" />
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-white tracking-tight tabular-nums">R$ {{ number_format(collect($chartData['profit'])->avg(), 2, ',', '.') }}</p>
        </div>
    </div>
</div>
