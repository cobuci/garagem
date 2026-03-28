@props(['targetBalance', 'goalMetrics'])

<div class="relative overflow-hidden bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 group hover:shadow-md transition-shadow duration-300">
    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 dark:bg-amber-900/10 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
    <div class="relative">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-amber-50 dark:bg-amber-900/40 rounded-xl">
                <x-icon name="trophy" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">{{ number_format($goalMetrics['percent'], 1) }}%</span>
            </div>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">{{ __('dashboard.monthly_goal') }}</h3>
        <div class="mt-2" x-data="{ editing: false }">
            <div x-show="!editing" @click="editing = true; $nextTick(() => $refs.input.focus())" class="cursor-pointer group/goal">
                <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none flex items-center gap-2">
                    R$ {{ number_format($targetBalance, 2, ',', '.') }}
                    <x-icon name="pencil" class="w-4 h-4 text-gray-400 opacity-0 group-hover/goal:opacity-100 transition-opacity" />
                </p>
            </div>
            <div x-show="editing" x-cloak class="mt-1">
                <input
                    x-ref="input"
                    type="number"
                    step="0.01"
                    class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg px-2 py-1 text-sm font-bold focus:ring-amber-500 focus:border-amber-500"
                    wire:model="targetBalance"
                    wire:change="updateTargetBalance"
                    @keydown.enter="editing = false"
                    @click.away="editing = false"
                >
            </div>

            <div class="mt-3">
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3 mb-2 p-0.5 shadow-inner relative overflow-hidden">
                    @if($goalMetrics['pending_sales'] > 0)
                        <div
                            class="absolute inset-y-0.5 left-0.5 rounded-full transition-all duration-1000 ease-out"
                            style="width: calc({{ min($goalMetrics['pending_percent'], 100) }}% - 4px); background: repeating-linear-gradient(90deg, #818cf8 0px, #818cf8 6px, transparent 6px, transparent 10px);"
                        ></div>
                    @endif
                    <div
                        class="absolute inset-y-0.5 left-0.5 bg-gradient-to-r from-amber-400 to-amber-600 rounded-full transition-all duration-1000 ease-out shadow-sm"
                        style="width: calc({{ min($goalMetrics['percent'], 100) }}% - 4px)"
                    ></div>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <p class="text-[10px] font-bold uppercase tracking-tight {{ $goalMetrics['reached'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400' }}">
                        @if($goalMetrics['reached'])
                            <span class="flex items-center gap-1">
                                <x-icon name="check-circle" class="w-3 h-3" />
                                {{ __('dashboard.goal_reached') }}
                            </span>
                        @else
                            {{ __('dashboard.remaining_to_goal', ['amount' => 'R$ ' . number_format($goalMetrics['remaining'], 2, ',', '.')]) }}
                        @endif
                    </p>
                    <div class="flex items-center gap-3 shrink-0">
                        @if($goalMetrics['pending_sales'] > 0)
                            <span class="flex items-center gap-1 text-[10px] font-bold text-indigo-400 dark:text-indigo-400 uppercase">
                                <x-icon name="clock" class="w-3 h-3" />
                                + R$ {{ number_format($goalMetrics['pending_sales'], 2, ',', '.') }}
                            </span>
                        @endif
                        @if(!$goalMetrics['reached'])
                            <p class="text-[10px] font-bold text-gray-400 uppercase">
                                {{ number_format($goalMetrics['percent'], 0) }}%
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
