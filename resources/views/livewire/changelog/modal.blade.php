<div x-data @open-changelog.window="$wire.reopen()">
    @if($isOpen && $currentItem)
        {{-- Backdrop --}}
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-data
            x-init="document.body.classList.add('overflow-hidden')"
            x-effect="if (!$wire.isOpen) document.body.classList.remove('overflow-hidden')"
        >
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="dismiss"></div>

            {{-- Modal card --}}
            <div class="relative z-10 w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                {{-- Close button --}}
                <button wire:click="dismiss"
                        class="absolute top-4 right-4 p-1.5 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors z-10">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>

                {{-- Header --}}
                <div class="px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 shrink-0">
                            <x-icon name="megaphone" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                                {{ __('changelog.title') }}
                            </p>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                                {{ $currentItem['changelog_title'] }}
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300">
                                    v{{ $currentItem['changelog_version'] }}
                                </span>
                            </h2>
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="px-6 py-5 min-h-[200px]">
                    @if($currentItem['image_path'])
                        <div class="mb-4 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                            <img src="{{ Storage::url($currentItem['image_path']) }}"
                                 alt="{{ $currentItem['title'] }}"
                                 class="w-full h-56 object-cover" />
                        </div>
                    @endif

                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">
                        {{ $currentItem['title'] }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                        {{ $currentItem['description'] }}
                    </p>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/40 border-t border-gray-100 dark:border-gray-700 flex items-center gap-3">

                    {{-- Progress dots --}}
                    <div class="flex items-center gap-1.5 shrink-0">
                        @for($i = 0; $i < $totalSteps; $i++)
                            <div class="rounded-full transition-all duration-300 {{ $i === $currentStep ? 'w-4 h-2 bg-indigo-500' : 'w-2 h-2 bg-gray-300 dark:bg-gray-600' }}"></div>
                        @endfor
                    </div>

                    {{-- Step counter --}}
                    <span class="text-xs text-gray-400 dark:text-gray-500 tabular-nums">
                        {{ __('changelog.step', ['current' => $currentStep + 1, 'total' => $totalSteps]) }}
                    </span>

                    {{-- Navigation --}}
                    <div class="flex items-center gap-2 ml-auto">
                        @if(!$isFirst)
                            <button wire:click="previous"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <x-icon name="chevron-left" class="w-4 h-4" />
                                {{ __('changelog.previous') }}
                            </button>
                        @endif

                        <button wire:click="next"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                            @if($isLast)
                                {{ __('changelog.finish') }}
                                <x-icon name="check" class="w-4 h-4" />
                            @else
                                {{ __('changelog.next') }}
                                <x-icon name="chevron-right" class="w-4 h-4" />
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
