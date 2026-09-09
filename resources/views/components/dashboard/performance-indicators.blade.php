@props(['dailyMetrics', 'monthlyMetrics', 'chartData'])

<div class="space-y-6">
    <div
        x-data="{ expanded: false }"
        @click="expanded = !expanded"
        class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group cursor-pointer transition-all duration-200 hover:shadow-md"
    >
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-indigo-50 dark:bg-indigo-950/40 rounded-lg text-indigo-600 dark:text-indigo-400">
                        <x-icon name="bolt" class="w-3.5 h-3.5" />
                    </div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('dashboard.yesterday') }}</h4>
                </div>
                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-300" x-bind:class="expanded ? 'rotate-180' : ''" />
            </div>

            <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight tabular-nums">R$ {{ number_format($dailyMetrics['previous_sales_total'], 2, ',', '.') }}</p>

            <div class="mt-2 flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('dashboard.profit') }}</span>
                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">R$ {{ number_format($dailyMetrics['previous_profit_total'], 2, ',', '.') }}</span>
            </div>

            <div
                class="grid transition-all duration-300 ease-in-out"
                x-bind:class="expanded ? 'grid-rows-[1fr] opacity-100 mt-4' : 'grid-rows-[0fr] opacity-0 mt-0'"
            >
                <div class="overflow-hidden">
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700/60 space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('dashboard.paid') }}</span>
                                <p class="text-sm font-bold text-gray-700 dark:text-gray-200 tabular-nums">R$ {{ number_format($dailyMetrics['previous_sales'], 2, ',', '.') }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">{{ __('dashboard.not_paid') }}</span>
                                <p class="text-sm font-bold text-amber-600 dark:text-amber-400 tabular-nums">R$ {{ number_format($dailyMetrics['previous_pending_sales'], 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100 dark:border-gray-700/40">
                            <div class="space-y-1">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('dashboard.profit_paid') }}</span>
                                <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">R$ {{ number_format($dailyMetrics['previous_profit'], 2, ',', '.') }}</p>
                            </div>
                            @if($dailyMetrics['previous_pending_profit'] > 0)
                                <div class="space-y-1">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">{{ __('dashboard.profit_pending') }}</span>
                                    <p class="text-sm font-bold text-amber-600 dark:text-amber-400 tabular-nums">R$ {{ number_format($dailyMetrics['previous_pending_profit'], 2, ',', '.') }}</p>
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
            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('dashboard.month') }}</h4>
            <div @class([
                'flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold tabular-nums',
                'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' => $monthlyMetrics['percent'] >= 0,
                'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400' => $monthlyMetrics['percent'] < 0,
            ])>
                <x-icon name="{{ $monthlyMetrics['percent'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="w-3.5 h-3.5" />
                {{ number_format(abs($monthlyMetrics['percent']), 1) }}%
            </div>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">{{ __('dashboard.total') }}</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">R$ {{ number_format($monthlyMetrics['total'], 2, ',', '.') }}</span>
            </div>
            @if($monthlyMetrics['pending_sales'] > 0)
                <div class="flex items-center justify-between bg-amber-50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-700/40 rounded-lg px-2.5 py-1.5">
                    <span class="flex items-center gap-1.5 text-xs text-amber-700 dark:text-amber-400 font-semibold uppercase tracking-wider">
                        <x-icon name="clock" class="w-3.5 h-3.5 shrink-0" />
                        {{ __('dashboard.not_paid') }}
                    </span>
                    <span class="text-xs font-bold text-amber-700 dark:text-amber-400 tabular-nums">R$ {{ number_format($monthlyMetrics['pending_sales'], 2, ',', '.') }}</span>
                </div>
            @endif
            <div class="w-full bg-gray-100 dark:bg-gray-700 h-2.5 rounded-full overflow-hidden p-0.5 border border-gray-100 dark:border-gray-700">
                <div class="relative h-full rounded-full overflow-hidden">
                    <div class="absolute inset-y-0 left-0 bg-amber-400/70 rounded-full transition-all duration-700" style="width: {{ $monthlyMetrics['bar_pending_percentage'] }}%"></div>
                    <div class="absolute inset-y-0 left-0 bg-sky-600 dark:bg-sky-500 rounded-full shadow-sm transition-all duration-700" style="width: {{ $monthlyMetrics['bar_paid_percentage'] }}%"></div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">{{ __('dashboard.last_month') }}</span>
                <div class="text-right">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tabular-nums">R$ {{ number_format($monthlyMetrics['previous_sales'], 2, ',', '.') }}</span>
                    @if($monthlyMetrics['previous_pending_sales'] > 0)
                        <span class="block text-xs text-amber-600 dark:text-amber-400 font-semibold tabular-nums">
                            + R$ {{ number_format($monthlyMetrics['previous_pending_sales'], 2, ',', '.') }} {{ __('dashboard.not_paid') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
