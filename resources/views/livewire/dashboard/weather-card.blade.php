<div>
    @if($this->current)
        <div class="flex items-center gap-4 group">
            <div
                class="flex items-center gap-3 bg-white/50 dark:bg-gray-800/50 backdrop-blur-md px-4 py-2 rounded-2xl border border-gray-100/50 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md hover:scale-105 cursor-pointer"
                x-on:click="$openModal('weatherHistoryModal')"
            >
                <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-sm">
                    <x-icon :name="$this->icon" class="h-6 w-6 text-white" />
                </div>
                <div class="flex flex-col">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter leading-none">
                            {{ round($this->current['temperature_2m']) }}°
                        </span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider leading-none">{{ $this->description }}</span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-[10px] font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-tight">{{ $this->current['city'] }}</span>
                        <span class="text-[9px] font-medium text-gray-400 dark:text-gray-500 italic">
                            {{ __('dashboard.weather.updated_at', ['time' => $this->lastUpdated]) }}
                        </span>
                    </div>
                </div>
                <button
                    wire:click.stop="loadWeather(true)"
                    wire:loading.attr="disabled"
                    class="ml-2 flex h-6 w-6 items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 transition-colors disabled:opacity-50"
                    title="{{ __('dashboard.weather.refresh') }}"
                >
                    <x-icon name="arrow-path" class="h-3.5 w-3.5" wire:loading.class="animate-spin" />
                </button>
            </div>
        </div>

        <x-modal-card name="weatherHistoryModal" title="{{ __('dashboard.weather.history') }}" max-width="lg" align="center" focus="false">
            <div class="space-y-4">
                <div class="flex items-center justify-between px-1">
                    <div class="flex flex-col">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                            {{ __('dashboard.weather.last_days') }}
                        </h3>
                        <p class="text-sm font-black text-gray-900 dark:text-white">
                            {{ $this->current['city'] }}
                        </p>
                    </div>
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl">
                        <x-icon name="calendar" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">
                    @forelse($this->history as $day)
                        <div class="flex items-center justify-between bg-white dark:bg-gray-900/50 p-4 transition-all hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <div class="flex items-center gap-4">
                                <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 shadow-sm border border-gray-100 dark:border-gray-600">
                                    <x-icon :name="$day['icon']" class="h-6 w-6 text-indigo-500" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white capitalize">
                                        {{ \Carbon\Carbon::parse($day['date'])->translatedFormat('l, d M') }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        {{ $day['description'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex flex-col items-end pr-4 border-r border-gray-100 dark:border-gray-800">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">
                                        {{ __('dashboard.weather.precipitation') }}
                                    </span>
                                    <span class="text-xs font-black text-blue-500">
                                        {{ $day['precipitation'] }}<span class="text-[10px] ml-0.5">mm</span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col items-center">
                                        <span class="text-[8px] font-bold text-red-400 uppercase tracking-widest leading-none mb-1">
                                            MAX
                                        </span>
                                        <span class="text-sm font-black text-gray-900 dark:text-white">
                                            {{ round($day['temp_max']) }}°
                                        </span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <span class="text-[8px] font-bold text-blue-400 uppercase tracking-widest leading-none mb-1">
                                            MIN
                                        </span>
                                        <span class="text-sm font-black text-gray-900 dark:text-white">
                                            {{ round($day['temp_min']) }}°
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>

            <x-slot name="footer" class="flex justify-end gap-x-4">
                <x-button flat label="{{ __('products.cancel') }}" x-on:click="$closeModal('weatherHistoryModal')" />
            </x-slot>
        </x-modal-card>
    @endif

    @if($hasError)
        <div class="flex items-center gap-4 group">
            <div class="flex items-center gap-3 bg-red-50/50 dark:bg-red-900/20 backdrop-blur-md px-4 py-2 rounded-2xl border border-red-100/50 dark:border-red-800/50 shadow-sm transition-all">
                <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-pink-600 shadow-sm">
                    <x-icon name="exclamation-triangle" class="h-6 w-6 text-white" />
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-red-600 dark:text-red-400 uppercase tracking-tight">{{ __('dashboard.weather.error') }}</span>
                    <button
                        wire:click="loadWeather"
                        wire:loading.attr="disabled"
                        class="text-[10px] font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 uppercase tracking-tight flex items-center gap-1 mt-0.5 transition-colors"
                    >
                        {{ __('dashboard.weather.refresh') }}
                        <x-icon name="arrow-path" class="h-3 w-3" wire:loading.class="animate-spin" />
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
