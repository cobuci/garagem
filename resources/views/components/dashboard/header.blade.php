@props(['name'])

<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __('dashboard.title') }}</h1>
        <p class="mt-1 text-gray-500 dark:text-gray-400 font-medium">{{ __('dashboard.welcome_back', ['name' => $name]) }}</p>
    </div>
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800">
            <span class="w-2 h-2 mr-2 rounded-full bg-indigo-500 animate-pulse"></span>
            {{ now()->translatedFormat('d M, Y') }}
        </span>
    </div>
</div>
