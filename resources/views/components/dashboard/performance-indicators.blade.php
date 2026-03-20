@props(['dailyMetrics', 'monthlyMetrics', 'chartData'])

<div class="space-y-6">
    <div class="bg-indigo-600 rounded-2xl p-6 shadow-lg shadow-indigo-100 dark:shadow-none relative overflow-hidden group">
        <div class="absolute right-0 bottom-0 opacity-10 transform translate-x-4 translate-y-4 transition-transform group-hover:scale-125 duration-700">
            <x-icon name="bolt" class="w-32 h-32 text-white" />
        </div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-indigo-100 text-[10px] font-bold uppercase tracking-wider">{{ __('dashboard.today') }}</h4>
                <x-icon name="bolt" class="w-4 h-4 text-indigo-300" />
            </div>
            <p class="text-white text-3xl font-black tracking-tight">R$ {{ number_format($dailyMetrics['sales'], 2, ',', '.') }}</p>
            <div class="mt-4 pt-4 border-t border-indigo-500/30">
                <div class="flex items-center justify-between text-indigo-100 text-xs font-bold">
                    <span class="opacity-80">{{ __('dashboard.yesterday') }}</span>
                    <span>R$ {{ number_format($dailyMetrics['previous_sales'], 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm group">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-gray-900 dark:text-white text-sm font-bold uppercase tracking-tighter">{{ __('dashboard.month') }}</h4>
            <div @class([
                'flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold',
                'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' => $monthlyMetrics['percent'] >= 0,
                'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400' => $monthlyMetrics['percent'] < 0,
            ])>
                <x-icon name="{{ $monthlyMetrics['percent'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="w-3 h-3" />
                {{ number_format(abs($monthlyMetrics['percent']), 1) }}%
            </div>
        </div>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium uppercase tracking-tight">{{ __('dashboard.sales') }}</span>
                <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">R$ {{ number_format($monthlyMetrics['sales'], 2, ',', '.') }}</span>
            </div>
            <div class="w-full bg-gray-50 dark:bg-gray-900 h-2.5 rounded-full overflow-hidden p-0.5 border border-gray-100 dark:border-gray-700">
                @php
                    $max = max($monthlyMetrics['sales'], $monthlyMetrics['previous_sales'], 1);
                    $percentage = ($monthlyMetrics['sales'] / $max) * 100;
                @endphp
                <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $percentage }}%"></div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium uppercase tracking-tight">{{ __('dashboard.last_month') }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-tighter">R$ {{ number_format($monthlyMetrics['previous_sales'], 2, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
