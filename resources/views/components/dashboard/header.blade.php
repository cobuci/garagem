@props(['name'])

<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __('dashboard.title') }}</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400 font-medium">{{ __('dashboard.welcome_back', ['name' => $name]) }}</p>
        </div>

        <livewire:dashboard.weather-card />
    </div>
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-bold bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-100 dark:border-gray-700 shadow-sm transition-all hover:shadow-md">
            <span class="w-2 h-2 mr-2 rounded-full bg-indigo-500 animate-pulse"></span>
            {{ now()->translatedFormat('d M, Y') }}
        </span>
    </div>
</div>
