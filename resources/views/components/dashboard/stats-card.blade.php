@props([
    'title',
    'value',
    'total' => null,
    'icon',
    'color' => 'blue',
    'trend' => null,
    'profit' => null,
    'totalProfit' => null,
    'pendingSales' => null,
    'pendingProfit' => null,
])

@php
    $colors = [
        'sky' => [
            'bg' => 'bg-sky-50 dark:bg-sky-950/40',
            'icon' => 'text-sky-600 dark:text-sky-400',
            'blob' => 'bg-sky-50 dark:bg-sky-950/20'
        ],
        'blue' => [
            'bg' => 'bg-sky-50 dark:bg-sky-950/40',
            'icon' => 'text-sky-600 dark:text-sky-400',
            'blob' => 'bg-sky-50 dark:bg-sky-950/20'
        ],
        'emerald' => [
            'bg' => 'bg-emerald-50 dark:bg-emerald-950/40',
            'icon' => 'text-emerald-600 dark:text-emerald-400',
            'blob' => 'bg-emerald-50 dark:bg-emerald-950/20'
        ],
        'indigo' => [
            'bg' => 'bg-indigo-50 dark:bg-indigo-950/40',
            'icon' => 'text-indigo-600 dark:text-indigo-400',
            'blob' => 'bg-indigo-50 dark:bg-indigo-950/20'
        ],
        'purple' => [
            'bg' => 'bg-indigo-50 dark:bg-indigo-950/40',
            'icon' => 'text-indigo-600 dark:text-indigo-400',
            'blob' => 'bg-indigo-50 dark:bg-indigo-950/20'
        ],
        'amber' => [
            'bg' => 'bg-amber-50 dark:bg-amber-950/40',
            'icon' => 'text-amber-600 dark:text-amber-400',
            'blob' => 'bg-amber-50 dark:bg-amber-950/20'
        ],
    ];

    $currentColor = $colors[$color] ?? $colors['sky'];
    $hasBreakdown = $pendingSales !== null && $pendingSales > 0;
@endphp

<div
    x-data="{ expanded: false }"
    @if($hasBreakdown) @click="expanded = !expanded" @endif
    @class([
        "relative overflow-hidden bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 group transition-all duration-200 h-full",
        "cursor-pointer hover:shadow-md" => $hasBreakdown,
        "hover:shadow-md" => !$hasBreakdown
    ])
>
    <div @class(["absolute -right-4 -top-4 w-24 h-24 rounded-full group-hover:scale-105 transition-transform duration-500 pointer-events-none", $currentColor['blob']])></div>
    <div class="relative">
        <div class="flex items-center justify-between mb-4">
            <div @class(["p-3 rounded-xl", $currentColor['bg']])>
                <x-icon :name="$icon" @class(["w-6 h-6", $currentColor['icon']]) />
            </div>

            @if($trend !== null)
                <div @class([
                    'flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold tabular-nums',
                    'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' => $trend >= 0,
                    'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400' => $trend < 0,
                ])>
                    <x-icon name="{{ $trend >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="w-3.5 h-3.5" />
                    {{ number_format(abs($trend), 1) }}%
                </div>
            @endif
        </div>

        <div class="flex items-center justify-between">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $title }}</h3>
            @if($hasBreakdown)
                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-300 ease-in-out" x-bind:class="expanded ? 'rotate-180' : ''" />
            @endif
        </div>

        <div class="mt-2">
            <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none tabular-nums">R$ {{ number_format($total ?? $value, 2, ',', '.') }}</p>
        </div>

        <div
            class="grid transition-all duration-300 ease-in-out"
            x-bind:class="expanded ? 'grid-rows-[1fr] opacity-100 mt-4' : 'grid-rows-[0fr] opacity-0 mt-0'"
        >
            <div class="overflow-hidden">
                <div class="pt-4 border-t border-gray-100 dark:border-gray-700/60 space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('dashboard.paid') }}</span>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200 tabular-nums">R$ {{ number_format($value, 2, ',', '.') }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">{{ __('dashboard.not_paid') }}</span>
                            <p class="text-sm font-bold text-amber-600 dark:text-amber-400 tabular-nums">R$ {{ number_format($pendingSales, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    @if($profit !== null)
                        <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100 dark:border-gray-700/40">
                            <div class="space-y-1">
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('dashboard.profit_paid') }}</span>
                                <p @class([
                                    'text-sm font-bold tabular-nums',
                                    'text-emerald-600 dark:text-emerald-400' => $color === 'emerald',
                                    'text-indigo-600 dark:text-indigo-400' => in_array($color, ['indigo', 'purple']),
                                    'text-sky-600 dark:text-sky-400' => in_array($color, ['sky', 'blue']),
                                ])>R$ {{ number_format($profit, 2, ',', '.') }}</p>
                            </div>
                            @if($pendingProfit !== null && $pendingProfit > 0)
                                <div class="space-y-1">
                                    <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">{{ __('dashboard.profit_pending') }}</span>
                                    <p class="text-sm font-bold text-amber-600 dark:text-amber-400 tabular-nums">R$ {{ number_format($pendingProfit, 2, ',', '.') }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($totalProfit !== null)
            <div
                class="transition-all duration-300 ease-in-out overflow-hidden"
                x-bind:class="expanded ? 'max-h-0 opacity-0 mt-0' : 'max-h-20 opacity-100 mt-3'"
            >
                <div class="flex flex-col">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('dashboard.profit') }}</span>
                    <span @class([
                        'font-bold tabular-nums text-sm',
                        'text-emerald-600 dark:text-emerald-400' => $color === 'emerald',
                        'text-indigo-600 dark:text-indigo-400' => in_array($color, ['indigo', 'purple']),
                        'text-sky-600 dark:text-sky-400' => in_array($color, ['sky', 'blue']),
                    ])>R$ {{ number_format($totalProfit, 2, ',', '.') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>
