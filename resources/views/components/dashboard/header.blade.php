@props(['name'])

<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __('dashboard.title') }}</h1>
        <p class="mt-1 text-gray-500 dark:text-gray-400 font-medium">{{ __('dashboard.welcome_back', ['name' => $name]) }}</p>
    </div>

    <div class="flex items-center gap-3">
        <livewire:dashboard.weather-card />
    </div>
</div>
