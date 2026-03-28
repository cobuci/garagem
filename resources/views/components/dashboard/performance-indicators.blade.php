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
            @if($dailyMetrics['pending_sales'] > 0)
                <div class="mt-2 inline-flex items-center gap-1.5 bg-amber-400/20 border border-amber-400/30 text-amber-200 rounded-lg px-2.5 py-1">
                    <x-icon name="clock" class="w-3 h-3 shrink-0" />
                    <span class="text-xs font-bold">{{ __('dashboard.not_paid') }}: R$ {{ number_format($dailyMetrics['pending_sales'], 2, ',', '.') }}</span>
                </div>
            @endif
            <div class="mt-4 pt-4 border-t border-indigo-500/30">
                <div class="flex items-center justify-between text-indigo-100 text-xs font-bold">
                    <span class="opacity-80">{{ __('dashboard.yesterday') }}</span>
                    <div class="text-right">
                        <span>R$ {{ number_format($dailyMetrics['previous_sales'], 2, ',', '.') }}</span>
                        @if($dailyMetrics['previous_pending_sales'] > 0)
                            <span class="block text-[10px] text-amber-300 font-medium">
                                + R$ {{ number_format($dailyMetrics['previous_pending_sales'], 2, ',', '.') }} {{ __('dashboard.not_paid') }}
                            </span>
                        @endif
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
                <span class="text-xs text-gray-500 font-medium uppercase tracking-tight">{{ __('dashboard.sales') }}</span>
                <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">R$ {{ number_format($monthlyMetrics['sales'], 2, ',', '.') }}</span>
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
                @php
                    $totalCurrent = $monthlyMetrics['sales'] + $monthlyMetrics['pending_sales'];
                    $totalPrevious = $monthlyMetrics['previous_sales'] + $monthlyMetrics['previous_pending_sales'];
                    $max = max($totalCurrent, $totalPrevious, 1);
                    $paidPercentage = ($monthlyMetrics['sales'] / $max) * 100;
                    $pendingPercentage = ($totalCurrent / $max) * 100;
                @endphp
                <div class="relative h-full rounded-full overflow-hidden">
                    <div class="absolute inset-y-0 left-0 bg-amber-400/60 rounded-full transition-all duration-1000" style="width: {{ $pendingPercentage }}%"></div>
                    <div class="absolute inset-y-0 left-0 bg-indigo-500 rounded-full shadow-sm transition-all duration-1000" style="width: {{ $paidPercentage }}%"></div>
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
