<div x-data="{ open: false, userDropdown: false, desktopUserDropdown: false }" class="relative">
    <!-- Mobile Top Bar -->
    <div class="lg:hidden fixed top-0 left-0 right-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 z-50">
        <div class="flex justify-between items-center p-4">
            <button @click="open = !open" class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <x-icon name="bars-3" class="w-6 h-6" x-show="!open" x-cloak />
                <x-icon name="x-mark" class="w-6 h-6" x-show="open" x-cloak />
            </button>

            <div class="relative">
                <button @click="userDropdown = !userDropdown" class="flex items-center focus:outline-none">
                    <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold border border-gray-300 dark:border-gray-600">
                        {{ substr(auth()->user()->name ?? auth()->user()->email, 0, 1) }}
                    </div>
                </button>

                <div x-cloak
                     x-show="userDropdown"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     @click.away="userDropdown = false"
                     class="absolute top-full right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50">
                    <div class="py-1">
                        <button @click="darkMode = !darkMode" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border-none bg-transparent cursor-pointer">
                            <x-icon x-show="!darkMode" name="moon" class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500" x-cloak />
                            <x-icon x-show="darkMode" name="sun" class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500" x-cloak />
                            <span x-text="darkMode ? '{{ __('sidebar.light_mode') }}' : '{{ __('sidebar.dark_mode') }}'"></span>
                        </button>
                        @can(\App\Enums\Permission::ViewSetting->value)
                            <a href="{{ route('settings.index') }}" wire:navigate class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <x-icon name="cog-6-tooth" class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500" />
                                {{ __('sidebar.settings') }}
                            </a>
                        @endcan
                        @can(\App\Enums\Permission::ViewAdmin->value)
                            <a href="{{ route('admin.index') }}" wire:navigate class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <x-icon name="shield-check" class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500" />
                                {{ __('sidebar.admin') }}
                            </a>
                        @endcan

                        @if(!app()->isProduction())
                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('sidebar.switch_user') }}
                            </div>
                            @foreach($this->availableUsers as $availableUser)
                                <button wire:click="switchUser({{ $availableUser->id }})" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border-none bg-transparent cursor-pointer">
                                    <div class="h-5 w-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-[10px] mr-3 font-bold border border-gray-300 dark:border-gray-600">
                                        {{ substr($availableUser->name ?? $availableUser->email, 0, 1) }}
                                    </div>
                                    <span class="truncate">{{ $availableUser->name ?? $availableUser->email }}</span>
                                </button>
                            @endforeach
                        @endif

                        <button wire:click="logout" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 border-none bg-transparent cursor-pointer">
                            <x-icon name="arrow-left-on-rectangle" class="mr-3 h-5 w-5 text-red-400" />
                            {{ __('sidebar.logout') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="open"
         x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden"></div>

    <div :class="open ? 'translate-x-0' : '-translate-x-full'"
         class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform lg:translate-x-0 transition duration-300 ease-in-out z-40 flex flex-col h-screen">

        <div class="p-6 flex items-center space-x-3">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                <x-icon name="bolt" class="w-5 h-5 text-white" />
            </div>
            <span class="text-xl font-bold text-gray-800 dark:text-white truncate">{{ config('app.name') }}</span>
        </div>

        <nav class="flex-1 px-4 space-y-8 mt-4 overflow-y-auto">
            @foreach($this->menuGroups as $group)
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 px-2">{{ $group['title'] }}</h3>
                    <div class="space-y-1">
                        @foreach($group['items'] as $item)
                            @php
                                $href = $item['route'] !== '#' ? route($item['route']) : '#';
                            @endphp
                            <a href="{{ $href }}"
                               @if($item['route'] !== '#') wire:navigate @endif
                               class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ $item['active'] ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                <x-icon name="{{ $item['icon'] }}" class="mr-3 h-5 w-5 {{ $item['active'] ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300' }}" />
                                <span class="flex-1 truncate">{{ $item['label'] }}</span>
                                @if(!empty($item['badge']))
                                    <span class="ml-auto inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- What's New --}}
            <div>
                <div class="space-y-1">
                    <a
                        href="{{ route('changelog.index') }}"
                        wire:navigate
                        class="w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white"
                    >
                        <x-icon name="megaphone" class="mr-3 h-5 w-5 text-indigo-400" />
                        {{ __('sidebar.whats_new') }}
                        @if($this->hasUnseenChangelogs)
                            <span class="ml-auto inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-indigo-500 text-white">
                                {{ __('sidebar.new') }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </nav>

        <div class="hidden lg:block p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="relative">
                <div @click="desktopUserDropdown = !desktopUserDropdown" class="flex items-center px-2 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition duration-150">
                    <div class="shrink-0">
                        <div class="h-9 w-9 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800">
                            {{ substr(auth()->user()->name ?? auth()->user()->email, 0, 1) }}
                        </div>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->name ?? __('sidebar.user') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="w-4 h-4 text-gray-400 transform transition-transform duration-200" :class="desktopUserDropdown ? 'rotate-180' : ''">
                        <x-icon name="chevron-up" class="w-4 h-4" />
                    </div>
                </div>

                <div x-cloak
                     x-show="desktopUserDropdown"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     @click.away="desktopUserDropdown = false"
                     class="absolute bottom-full left-0 mb-2 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-50 overflow-hidden">
                    <div class="py-1">
                        <button @click="darkMode = !darkMode" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border-none bg-transparent cursor-pointer">
                            <x-icon x-show="!darkMode" name="moon" class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500" x-cloak />
                            <x-icon x-show="darkMode" name="sun" class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500" x-cloak />
                            <span x-text="darkMode ? '{{ __('sidebar.light_mode') }}' : '{{ __('sidebar.dark_mode') }}'"></span>
                        </button>
                        @can(\App\Enums\Permission::ViewSetting->value)
                            <a href="{{ route('settings.index') }}" wire:navigate class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <x-icon name="cog-6-tooth" class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500" />
                                {{ __('sidebar.settings') }}
                            </a>
                        @endcan
                        @can(\App\Enums\Permission::ViewAdmin->value)
                            <a href="{{ route('admin.index') }}" wire:navigate class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <x-icon name="shield-check" class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500" />
                                {{ __('sidebar.admin') }}
                            </a>
                        @endcan

                        @if(!app()->isProduction())
                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('sidebar.switch_user') }}
                            </div>
                            @foreach($this->availableUsers as $availableUser)
                                <button wire:click="switchUser({{ $availableUser->id }})" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border-none bg-transparent cursor-pointer">
                                    <div class="h-5 w-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-[10px] mr-3 font-bold border border-gray-300 dark:border-gray-600">
                                        {{ substr($availableUser->name ?? $availableUser->email, 0, 1) }}
                                    </div>
                                    <span class="truncate">{{ $availableUser->name ?? $availableUser->email }}</span>
                                </button>
                            @endforeach
                        @endif

                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                        <button wire:click="logout" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 border-none bg-transparent cursor-pointer">
                            <x-icon name="arrow-left-on-rectangle" class="mr-3 h-5 w-5 text-red-400" />
                            {{ __('sidebar.logout') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
