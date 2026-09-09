@props(['targetBalance', 'goalMetrics'])

<div class="relative overflow-hidden bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 group hover:shadow-md transition-shadow duration-200">
    <div @class([
        "absolute -right-4 -top-4 w-24 h-24 rounded-full group-hover:scale-105 transition-transform duration-500 pointer-events-none",
        $goalMetrics['reached'] ? 'bg-emerald-50 dark:bg-emerald-950/20' : 'bg-sky-50 dark:bg-sky-950/20'
    ])></div>
    <div class="relative">
        <div class="flex items-center justify-between mb-4">
            <div @class([
                "p-3 rounded-xl",
                $goalMetrics['reached'] ? 'bg-emerald-50 dark:bg-emerald-950/40' : 'bg-sky-50 dark:bg-sky-950/40'
            ])>
                <x-icon name="trophy" @class([
                    "w-6 h-6",
                    $goalMetrics['reached'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-sky-600 dark:text-sky-400'
                ]) />
            </div>
            <div class="text-right">
                <span @class([
                    "text-xs font-bold tabular-nums",
                    $goalMetrics['reached'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-sky-600 dark:text-sky-400'
                ])>{{ number_format($goalMetrics['percent'], 1) }}%</span>
            </div>
        </div>
        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('dashboard.monthly_goal') }}</h3>
        <div class="mt-2" x-data="{ editing: false }">
            <div x-show="!editing" @click="editing = true; $nextTick(() => $refs.input.focus())" class="cursor-pointer group/goal">
                <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none tabular-nums flex items-center gap-2">
                    R$ {{ number_format($targetBalance, 2, ',', '.') }}
                    <x-icon name="pencil" class="w-4 h-4 text-gray-400 opacity-0 group-hover/goal:opacity-100 transition-opacity" />
                </p>
            </div>
            <div x-show="editing" x-cloak class="mt-1">
                <input
                    x-ref="input"
                    type="number"
                    step="0.01"
                    class="w-full max-w-xs bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg px-2.5 py-1 text-sm font-bold tabular-nums focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none"
                    wire:model="targetBalance"
                    wire:change="updateTargetBalance"
                    @keydown.enter="editing = false"
                    @click.away="editing = false"
                >
            </div>

            <div class="mt-4">
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 mb-2.5 p-0.5 shadow-inner relative overflow-hidden">
                    @if($goalMetrics['pending_sales'] > 0)
                        <div
                            class="absolute inset-y-0.5 left-0.5 rounded-full transition-all duration-700 ease-out"
                            style="width: calc({{ min($goalMetrics['pending_percent'], 100) }}% - 4px); background: repeating-linear-gradient(90deg, #f59e0b 0px, #f59e0b 6px, transparent 6px, transparent 10px);"
                        ></div>
                    @endif
                    <div
                        @class([
                            "absolute inset-y-0.5 left-0.5 rounded-full transition-all duration-700 ease-out shadow-sm",
                            $goalMetrics['reached'] ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-sky-500 dark:bg-sky-400'
                        ])
                        style="width: calc({{ min($goalMetrics['percent'], 100) }}% - 4px)"
                    ></div>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs font-semibold uppercase tracking-wider {{ $goalMetrics['reached'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}">
                        @if($goalMetrics['reached'])
                            <span class="flex items-center gap-1 font-bold">
                                <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                {{ __('dashboard.goal_reached') }}
                            </span>
                        @else
                            <span class="tabular-nums">
                                {{ __('dashboard.remaining_to_goal', ['amount' => 'R$ ' . number_format($goalMetrics['remaining'], 2, ',', '.')]) }}
                            </span>
                        @endif
                    </p>
                    <div class="flex items-center gap-3 shrink-0">
                        @if($goalMetrics['pending_sales'] > 0)
                            <span class="flex items-center gap-1 text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider tabular-nums">
                                <x-icon name="clock" class="w-3.5 h-3.5" />
                                + R$ {{ number_format($goalMetrics['pending_sales'], 2, ',', '.') }}
                            </span>
                        @endif
                        @if(!$goalMetrics['reached'])
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tabular-nums">
                                {{ number_format($goalMetrics['percent'], 0) }}%
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
