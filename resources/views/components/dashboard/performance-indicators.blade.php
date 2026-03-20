@props(['dailyMetrics', 'monthlyMetrics', 'chartData'])

<div class="space-y-6">
    <div class="bg-indigo-600 rounded-2xl p-6 shadow-lg shadow-indigo-200 dark:shadow-none relative overflow-hidden group">
        <div class="absolute right-0 bottom-0 opacity-10 transform translate-x-4 translate-y-4 transition-transform group-hover:scale-110">
            <x-icon name="bolt" class="w-32 h-32 text-white" />
        </div>
        <div class="relative">
            <h4 class="text-indigo-100 text-sm font-bold uppercase tracking-wider mb-1">{{ __('dashboard.today') }}</h4>
            <p class="text-white text-2xl font-black">R$ {{ number_format($dailyMetrics['sales'], 2, ',', '.') }}</p>
            <div class="mt-4 pt-4 border-t border-indigo-500/30">
                <div class="flex items-center justify-between text-indigo-100 text-xs font-bold">
                    <span>{{ __('dashboard.yesterday') }}</span>
                    <span>R$ {{ number_format($dailyMetrics['previous_sales'], 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
        <h4 class="text-gray-900 dark:text-white text-sm font-bold mb-4">{{ __('dashboard.month') }}</h4>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium">{{ __('dashboard.sales') }}</span>
                <span class="text-xs font-bold text-gray-900 dark:text-white">R$ {{ number_format($monthlyMetrics['sales'], 2, ',', '.') }}</span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                @php
                    $max = max($monthlyMetrics['sales'], $monthlyMetrics['previous_sales'], 1);
                    $percentage = ($monthlyMetrics['sales'] / $max) * 100;
                @endphp
                <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium">{{ __('dashboard.last_month') }}</span>
                <span class="text-xs font-bold text-gray-900 dark:text-white">R$ {{ number_format($monthlyMetrics['previous_sales'], 2, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
