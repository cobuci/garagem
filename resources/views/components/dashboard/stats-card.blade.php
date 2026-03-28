@props([
    'title',
    'value',
    'icon',
    'color' => 'blue',
    'trend' => null,
    'profit' => null,
    'previousProfit' => null,
    'pendingSales' => null,
])

@php
    $colors = [
        'blue' => [
            'bg' => 'bg-blue-50 dark:bg-blue-900/40',
            'icon' => 'text-blue-600 dark:text-blue-400',
            'blob' => 'bg-blue-50 dark:bg-blue-900/10'
        ],
        'emerald' => [
            'bg' => 'bg-emerald-50 dark:bg-emerald-900/40',
            'icon' => 'text-emerald-600 dark:text-emerald-400',
            'blob' => 'bg-emerald-50 dark:bg-emerald-900/10'
        ],
        'purple' => [
            'bg' => 'bg-purple-50 dark:bg-purple-900/40',
            'icon' => 'text-purple-600 dark:text-purple-400',
            'blob' => 'bg-purple-50 dark:bg-purple-900/10'
        ],
        'amber' => [
            'bg' => 'bg-amber-50 dark:bg-amber-900/40',
            'icon' => 'text-amber-600 dark:text-amber-400',
            'blob' => 'bg-amber-50 dark:bg-amber-900/10'
        ],
    ];

    $currentColor = $colors[$color] ?? $colors['blue'];
@endphp

<div class="relative overflow-hidden bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 group hover:shadow-md transition-shadow duration-300 h-full">
    <div @class(["absolute -right-4 -top-4 w-24 h-24 rounded-full group-hover:scale-110 transition-transform duration-500", $currentColor['blob']])></div>
    <div class="relative">
        <div class="flex items-center justify-between mb-4">
            <div @class(["p-3 rounded-xl", $currentColor['bg']])>
                <x-icon :name="$icon" @class(["w-6 h-6", $currentColor['icon']]) />
            </div>

            @if($trend !== null)
                <div class="flex flex-col items-end">
                    <div @class([
                        'flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold',
                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' => $trend >= 0,
                        'bg-red-50 text-red-700 dark:bg-red-900/40 dark:text-red-400' => $trend < 0,
                    ])>
                        <x-icon name="{{ $trend >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="w-3 h-3" />
                        {{ number_format(abs($trend), 1) }}%
                    </div>
                </div>
            @endif
        </div>

        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">{{ $title }}</h3>
        <div class="mt-2">
            <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none">R$ {{ number_format($value, 2, ',', '.') }}</p>
            @if($pendingSales !== null && $pendingSales > 0)
                <div class="mt-1.5 inline-flex items-center gap-1 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-md px-2 py-0.5">
                    <x-icon name="clock" class="w-3 h-3 shrink-0" />
                    <span class="text-xs font-bold whitespace-nowrap">+ R$ {{ number_format($pendingSales, 2, ',', '.') }} {{ __('dashboard.not_paid') }}</span>
                </div>
            @endif
        </div>

        @if($profit !== null)
            <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                <div class="flex flex-col">
                    <span class="text-gray-400 text-[10px] uppercase font-bold">{{ __('dashboard.profit') }}</span>
                    <span @class([
                        'font-bold',
                        'text-emerald-600 dark:text-emerald-400' => $color === 'emerald',
                        'text-purple-600 dark:text-purple-400' => $color === 'purple',
                    ])>R$ {{ number_format($profit, 2, ',', '.') }}</span>
                </div>
                <div class="flex flex-col border-l border-gray-100 dark:border-gray-700 pl-2">
                    <span class="text-gray-400 text-[10px] uppercase font-bold">{{ __('dashboard.previous_profit') }}</span>
                    <span class="font-bold text-gray-500">R$ {{ number_format($previousProfit, 2, ',', '.') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>
