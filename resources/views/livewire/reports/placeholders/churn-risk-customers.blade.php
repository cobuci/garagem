<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 lg:col-span-2">
    <div class="flex items-center justify-between mb-6">
        <div class="space-y-2">
            <div class="h-6 w-56 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
            <div class="h-4 w-40 bg-gray-100 dark:bg-gray-600 rounded animate-pulse"></div>
        </div>
    </div>

    <div class="space-y-3">
        <div class="h-10 w-full bg-gray-100 dark:bg-gray-700 rounded-lg animate-pulse"></div>
        @for ($i = 0; $i < 5; $i++)
            <div class="h-14 w-full bg-gray-50 dark:bg-gray-900/50 rounded-lg animate-pulse opacity-{{ 100 - ($i * 15) }}"></div>
        @endfor
    </div>
</div>
