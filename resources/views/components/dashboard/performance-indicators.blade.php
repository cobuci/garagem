@props(['dailyMetrics', 'monthlyMetrics', 'chartData'])

<div class="space-y-6">
    <div
        x-data="{ expanded: false }"
        @click="expanded = !expanded"
        class="bg-indigo-600 rounded-2xl p-6 shadow-lg shadow-indigo-100 dark:shadow-none relative overflow-hidden group cursor-pointer transition-all duration-300"
    >
        <div class="absolute right-0 bottom-0 opacity-10 transform translate-x-4 translate-y-4 transition-transform group-hover:scale-125 duration-700">
            <x-icon name="bolt" class="w-32 h-32 text-white" />
        </div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-indigo-100 text-[10px] font-bold uppercase tracking-wider">{{ __('dashboard.yesterday') }}</h4>
                <div class="flex items-center gap-2">
                    <x-icon name="chevron-down" class="w-3 h-3 text-indigo-300 transition-transform duration-300" x-bind:class="expanded ? 'rotate-180' : ''" />
                    <x-icon name="bolt" class="w-4 h-4 text-indigo-300" />
                </div>
            </div>
            <p class="text-white text-3xl font-black tracking-tight">R$ {{ number_format($dailyMetrics['previous_sales_total'], 2, ',', '.') }}</p>

            <div class="mt-2 flex items-center gap-2">
                <span class="text-indigo-200 text-xs font-bold uppercase tracking-tight">{{ __('dashboard.profit') }}</span>
                <span class="text-white text-sm font-black">R$ {{ number_format($dailyMetrics['previous_profit_total'], 2, ',', '.') }}</span>
            </div>

            <div
                class="grid transition-all duration-300 ease-in-out"
                x-bind:class="expanded ? 'grid-rows-[1fr] opacity-100 mt-4' : 'grid-rows-[0fr] opacity-0 mt-0'"
            >
                <div class="overflow-hidden">
                    <div class="pt-4 border-t border-indigo-500/30 space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold text-indigo-200 uppercase tracking-tight">{{ __('dashboard.paid') }}</span>
                                <p class="text-sm font-bold text-white">R$ {{ number_format($dailyMetrics['previous_sales'], 2, ',', '.') }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold text-amber-300 uppercase tracking-tight">{{ __('dashboard.not_paid') }}</span>
                                <p class="text-sm font-bold text-amber-300">R$ {{ number_format($dailyMetrics['previous_pending_sales'], 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2 border-t border-indigo-500/20">
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold text-indigo-200 uppercase tracking-tight">{{ __('dashboard.profit_paid') }}</span>
                                <p class="text-sm font-bold text-white">R$ {{ number_format($dailyMetrics['previous_profit'], 2, ',', '.') }}</p>
                            </div>
                            @if($dailyMetrics['previous_pending_profit'] > 0)
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-amber-300 uppercase tracking-tight">{{ __('dashboard.profit_pending') }}</span>
                                    <p class="text-sm font-bold text-amber-300">R$ {{ number_format($dailyMetrics['previous_pending_profit'], 2, ',', '.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
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
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium uppercase tracking-tight">{{ __('dashboard.total') }}</span>
                <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">R$ {{ number_format($monthlyMetrics['total'], 2, ',', '.') }}</span>
            </div>
            @if($monthlyMetrics['pending_sales'] > 0)
                <div class="flex items-center justify-between bg-amber-50 dark:bg-amber-900/20 border border-amber-200/60 dark:border-amber-700/40 rounded-lg px-2.5 py-1.5">
                    <span class="flex items-center gap-1.5 text-xs text-amber-700 dark:text-amber-400 font-medium uppercase tracking-tight">
                        <x-icon name="clock" class="w-3 h-3 shrink-0" />
                        {{ __('dashboard.not_paid') }}
                    </span>
                    <span class="text-xs font-black text-amber-700 dark:text-amber-400 uppercase tracking-tighter">R$ {{ number_format($monthlyMetrics['pending_sales'], 2, ',', '.') }}</span>
                </div>
            @endif
            <div class="w-full bg-gray-50 dark:bg-gray-900 h-2.5 rounded-full overflow-hidden p-0.5 border border-gray-100 dark:border-gray-700">
                <div class="relative h-full rounded-full overflow-hidden">
                    <div class="absolute inset-y-0 left-0 bg-amber-400/60 rounded-full transition-all duration-1000" style="width: {{ $monthlyMetrics['bar_pending_percentage'] }}%"></div>
                    <div class="absolute inset-y-0 left-0 bg-indigo-500 rounded-full shadow-sm transition-all duration-1000" style="width: {{ $monthlyMetrics['bar_paid_percentage'] }}%"></div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium uppercase tracking-tight">{{ __('dashboard.last_month') }}</span>
                <div class="text-right">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-tighter">R$ {{ number_format($monthlyMetrics['previous_sales'], 2, ',', '.') }}</span>
                    @if($monthlyMetrics['previous_pending_sales'] > 0)
                        <span class="block text-[10px] text-amber-500 dark:text-amber-400 font-medium">
                            + R$ {{ number_format($monthlyMetrics['previous_pending_sales'], 2, ',', '.') }} {{ __('dashboard.not_paid') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
