<div>
    @if($this->current)
        <div class="flex items-center gap-4 group">
            <div
                class="flex items-center gap-3 bg-white dark:bg-gray-800 px-3.5 py-2 rounded-xl border border-gray-200/80 dark:border-gray-700 shadow-sm hover:border-gray-300 dark:hover:border-gray-600 transition-colors cursor-pointer"
                x-on:click="$openModal('weatherHistoryModal')"
            >
                <div class="flex items-center gap-2 pr-3 mr-1 border-r border-gray-100 dark:border-gray-700">
                    <span class="inline-flex items-center text-xs font-semibold text-gray-700 dark:text-gray-300">
                        {{ now()->translatedFormat('d M, Y') }}
                    </span>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400">
                    <x-icon :name="$this->icon" class="h-5 w-5" />
                </div>
                <div class="flex flex-col">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-xl font-bold text-gray-900 dark:text-white tracking-tight leading-none tabular-nums">
                            {{ round($this->current['temperature_2m']) }}°
                        </span>
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider leading-none">{{ $this->description }}</span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs font-semibold text-sky-600 dark:text-sky-400 uppercase tracking-wider">{{ $this->current['city'] }}</span>
                        <span class="text-xs font-normal text-gray-400 dark:text-gray-500">
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
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('dashboard.weather.last_days') }}
                        </h3>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ $this->current['city'] }}
                        </p>
                    </div>
                    <div class="p-2 bg-sky-50 dark:bg-sky-950/40 rounded-xl text-sky-600 dark:text-sky-400">
                        <x-icon name="calendar" class="h-5 w-5" />
                    </div>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800 border border-gray-100 dark:border-gray-800 rounded-xl overflow-hidden shadow-sm">
                    @forelse($this->history as $day)
                        <div class="flex items-center justify-between bg-white dark:bg-gray-900/50 p-4 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                    <x-icon :name="$day['icon']" class="h-5 w-5 text-sky-600 dark:text-sky-400" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white capitalize">
                                        {{ \Carbon\Carbon::parse($day['date'])->translatedFormat('l, d M') }}
                                    </span>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ $day['description'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex flex-col items-end pr-4 border-r border-gray-100 dark:border-gray-800">
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider leading-none mb-1">
                                        {{ __('dashboard.weather.precipitation') }}
                                    </span>
                                    <span class="text-xs font-bold text-sky-600 dark:text-sky-400 tabular-nums">
                                        {{ $day['precipitation'] }}<span class="text-xs ml-0.5">mm</span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-semibold text-red-500 dark:text-red-400 uppercase tracking-wider leading-none mb-1">
                                            MAX
                                        </span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                            {{ round($day['temp_max']) }}°
                                        </span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-semibold text-sky-500 dark:text-sky-400 uppercase tracking-wider leading-none mb-1">
                                            MIN
                                        </span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
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

    @if($noLocation)
        <div class="flex items-center gap-4 group">
            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 px-3.5 py-2 rounded-xl border border-gray-200/80 dark:border-gray-700 shadow-sm transition-colors">
                <div class="flex items-center gap-2 pr-3 mr-1 border-r border-gray-100 dark:border-gray-700">
                    <span class="inline-flex items-center text-xs font-semibold text-gray-700 dark:text-gray-300">
                        {{ now()->translatedFormat('d M, Y') }}
                    </span>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400">
                    <x-icon name="map-pin" class="h-5 w-5" />
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('dashboard.weather.no_location') }}</span>
                    <a href="{{ route('settings.index') }}" wire:navigate class="text-xs font-semibold text-sky-600 hover:text-sky-700 dark:text-sky-400 uppercase tracking-wider flex items-center gap-1 mt-0.5 transition-colors">
                        {{ __('settings.settings') }}
                        <x-icon name="arrow-right" class="h-3 w-3" />
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if($hasError)
        <div class="flex items-center gap-4 group">
            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 px-3.5 py-2 rounded-xl border border-red-200 dark:border-red-900/60 shadow-sm transition-colors">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400">
                    <x-icon name="exclamation-triangle" class="h-5 w-5" />
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">{{ __('dashboard.weather.error') }}</span>
                    <button
                        wire:click="loadWeather"
                        wire:loading.attr="disabled"
                        class="text-xs font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 uppercase tracking-wider flex items-center gap-1 mt-0.5 transition-colors"
                    >
                        {{ __('dashboard.weather.refresh') }}
                        <x-icon name="arrow-path" class="h-3 w-3" wire:loading.class="animate-spin" />
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
