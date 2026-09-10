<div class="py-12 w-full max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 self-start">
    <div class="mb-12">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ __('changelog.history_title') }}
        </h1>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            {{ __('changelog.history_subtitle') }}
        </p>
    </div>

    @if($changelogs->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <x-icon name="megaphone" class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" />
            <p class="text-gray-500 dark:text-gray-400">
                {{ __('changelog.no_changelogs') }}
            </p>
        </div>
    @else
        <div class="relative w-full">
            {{-- Timeline vertical line --}}
            <div class="absolute top-0 bottom-0 left-4 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

            <div class="space-y-6">
                @foreach($changelogs as $index => $changelog)
                    <div x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }" class="relative pl-12 w-full">
                        {{-- Timeline dot --}}
                        <div class="absolute left-0 top-5 w-8 h-8 rounded-full flex items-center justify-center z-10"
                             :class="open ? 'bg-indigo-600' : 'bg-white dark:bg-gray-900 border-2 border-indigo-400'"
                        >
                            <x-icon name="megaphone" class="w-4 h-4 transition-colors" ::class="open ? 'text-white' : 'text-indigo-400'" />
                        </div>

                        {{-- Card --}}
                        <div class="w-full bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-shadow duration-300 min-w-0"
                             :class="open ? 'ring-2 ring-indigo-500/20' : ''">

                            {{-- Header --}}
                            <div @click="open = !open"
                                 class="p-5 cursor-pointer flex items-center justify-between group select-none">
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-3 mb-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 shrink-0">
                                            v{{ $changelog->version }}
                                        </span>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                            {{ $changelog->released_at->translatedFormat('d M, Y') }}
                                        </span>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                                        {{ $changelog->title }}
                                    </h3>
                                </div>
                                <x-icon name="chevron-down"
                                        class="w-5 h-5 text-gray-400 transition-transform duration-300 shrink-0 ml-4"
                                        ::class="open ? 'rotate-180' : ''" />
                            </div>

                            {{-- Content --}}
                            <div x-show="open" x-collapse x-cloak>
                                <div class="px-5 pb-6">
                                    <div class="h-px bg-gray-100 dark:bg-gray-700 mb-5"></div>
                                    <div class="space-y-5">
                                        @foreach($changelog->items as $item)
                                            <div class="flex gap-3">
                                                <div class="shrink-0 mt-1.5">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-0.5">
                                                        {{ $item->title }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                                                        {{ $item->description }}
                                                    </p>
                                                    @if($item->image_path)
                                                        <div class="mt-3 rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700">
                                                            <img src="{{ Storage::url($item->image_path) }}"
                                                                 class="w-full h-auto object-cover" />
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
