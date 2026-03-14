<div x-data="{ open: false }" class="relative">
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button @click="open = !open" class="p-2 rounded-md bg-white shadow-md text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <x-icon name="bars-3" class="w-6 h-6" x-show="!open" />
            <x-icon name="x-mark" class="w-6 h-6" x-show="open" />
        </button>
    </div>

    <div x-show="open"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden"></div>

    <div :class="open ? 'translate-x-0' : '-translate-x-full'"
         class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 transform lg:translate-x-0 lg:static lg:inset-0 transition duration-300 ease-in-out z-40 flex flex-col h-screen">

        <div class="p-6 flex items-center space-x-3">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <x-icon name="bolt" class="w-5 h-5 text-white" />
            </div>
            <span class="text-xl font-bold text-gray-800">{{ config('app.name') }}</span>
        </div>

        <nav class="flex-1 px-4 space-y-8 mt-4 overflow-y-auto">
            @foreach($this->menuGroups as $group)
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-2">{{ $group['title'] }}</h3>
                    <div class="space-y-1">
                        @foreach($group['items'] as $item)
                            @php
                                $href = $item['route'] !== '#' ? route($item['route']) : '#';
                            @endphp
                            <a href="{{ $href }}"
                               @if($item['route'] !== '#') wire:navigate @endif
                               class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ $item['active'] ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <x-icon name="{{ $item['icon'] }}" class="mr-3 h-5 w-5 {{ $item['active'] ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="p-4 border-t border-gray-200">
            <div class="space-y-1">
                <a href="#" class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <x-icon name="cog-6-tooth" class="mr-3 h-5 w-5 text-gray-400" />
                    {{ __('sidebar.settings') }}
                </a>
                <button wire:click="logout"
                        class="w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 hover:text-red-700">
                    <x-icon name="arrow-left-on-rectangle" class="mr-3 h-5 w-5 text-red-400" />
                    {{ __('sidebar.logout') }}
                </button>
            </div>

            <div class="mt-4 flex items-center px-2">
                <div class="shrink-0">
                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold">
                        {{ substr(auth()->user()->name ?? auth()->user()->email, 0, 1) }}
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-700 truncate w-32">{{ auth()->user()->name ?? __('sidebar.user') }}</p>
                    <p class="text-xs text-gray-500 truncate w-32">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
