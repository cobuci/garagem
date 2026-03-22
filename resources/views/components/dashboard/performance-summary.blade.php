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
        <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm group hover:border-indigo-100 dark:hover:border-indigo-900 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('dashboard.avg_monthly_sales') }}</span>
                <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg group-hover:scale-110 transition-transform">
                    <x-icon name="chart-bar" class="w-4 h-4 text-indigo-500" />
                </div>
            </div>
            <p class="text-xl font-black text-gray-900 dark:text-white tracking-tight">R$ {{ number_format(collect($chartData['sales'])->avg(), 2, ',', '.') }}</p>
        </div>

        <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm group hover:border-emerald-100 dark:hover:border-emerald-900 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('dashboard.avg_monthly_profit') }}</span>
                <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg group-hover:scale-110 transition-transform">
                    <x-icon name="currency-dollar" class="w-4 h-4 text-emerald-500" />
                </div>
            </div>
            <p class="text-xl font-black text-gray-900 dark:text-white tracking-tight">R$ {{ number_format(collect($chartData['profit'])->avg(), 2, ',', '.') }}</p>
        </div>
    </div>
</div>
