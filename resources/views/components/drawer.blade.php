@props(['id', 'title', 'maxWidth' => 'max-w-lg'])

<div
    x-data="{
        show: @entangle($attributes->wire('model')),
        close() { this.show = false }
    }"
    x-show="show"
    x-on:keydown.escape.window="close()"
    class="fixed inset-0 overflow-hidden z-40"
    style="display: none;"
    x-cloak
>
    <div
        x-show="show"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-gray-900/40 transition-opacity"
        x-on:click="close()"
    ></div>

    <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
        <div
            x-show="show"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="w-screen {{ $maxWidth }} bg-white shadow-2xl flex flex-col border-l border-gray-100"
        >
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <h2 class="text-lg font-bold text-gray-900">{{ $title }}</h2>
                <button x-on:click="close()" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <x-icon name="x-mark" class="w-6 h-6" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
