@props(['title', 'name', 'hint' => null])

<div
    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-2xs transition-all duration-150"
    :class="active === '{{ $name }}' ? 'ring-1 ring-sky-500/20' : ''"
>
    <button
        type="button"
        @click="active = active === '{{ $name }}' ? null : '{{ $name }}'"
        :aria-expanded="active === '{{ $name }}'"
        class="w-full flex items-center justify-between gap-3 px-4 py-3.5 text-left hover:bg-gray-50/80 dark:hover:bg-gray-700/40 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-inset"
    >
        <span class="font-semibold text-sm text-gray-900 dark:text-white" :class="active === '{{ $name }}' ? 'text-sky-700 dark:text-sky-400' : ''">{{ $title }}</span>
        <x-icon
            name="chevron-down"
            class="w-4 h-4 shrink-0 text-gray-400 transition-transform duration-200"
            x-bind:class="active === '{{ $name }}' ? 'rotate-180 text-sky-600 dark:text-sky-400' : ''"
        />
    </button>

    <div
        x-show="active === '{{ $name }}'"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="px-4 pb-5 pt-3 border-t border-gray-100 dark:border-gray-700/60"
    >
        @if ($hint)
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3.5 leading-relaxed">{{ $hint }}</p>
        @endif
        <div>
            {{ $slot }}
        </div>
    </div>
</div>
