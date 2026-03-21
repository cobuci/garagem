<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
        <div class="space-y-2">
            <div class="h-6 w-48 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
            <div class="h-4 w-32 bg-gray-100 dark:bg-gray-800 rounded animate-pulse"></div>
        </div>
        <div class="h-10 w-40 bg-gray-100 dark:bg-gray-800 rounded-lg animate-pulse"></div>
    </div>

    <div class="h-80 w-full bg-gray-50 dark:bg-gray-900/50 rounded-xl animate-pulse flex flex-col items-center justify-center space-y-4 px-6">
        @for ($i = 0; $i < 5; $i++)
            <div class="w-full h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse opacity-{{ 100 - ($i * 20) }}"></div>
        @endfor
    </div>
</div>
