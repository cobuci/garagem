@props(['title', 'name', 'hint' => null])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
    <button
        type="button"
        @click="active = active === '{{ $name }}' ? null : '{{ $name }}'"
        :aria-expanded="active === '{{ $name }}'"
        class="w-full flex items-center justify-between gap-3 px-4 py-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700/40 transition"
    >
        <span class="font-semibold text-gray-900 dark:text-white">{{ $title }}</span>
        <x-icon name="chevron-down" class="w-5 h-5 shrink-0 text-gray-400 transition-transform duration-200" x-bind:class="active === '{{ $name }}' ? 'rotate-180' : ''" />
    </button>

    <div
        x-show="active === '{{ $name }}'"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="px-4 pb-5 pt-2 border-t border-gray-100 dark:border-gray-700/60"
    >
        @if ($hint)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-4">{{ $hint }}</p>
        @endif
        <div class="mt-2">
            {{ $slot }}
        </div>
    </div>
</div>
