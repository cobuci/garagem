<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 lg:col-span-2">
    <div class="flex items-center justify-between mb-6">
        <div class="space-y-2">
            <div class="h-6 w-48 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
            <div class="h-4 w-32 bg-gray-100 dark:bg-gray-800 rounded animate-pulse"></div>
        </div>
        <div class="h-10 w-40 bg-gray-100 dark:bg-gray-800 rounded-lg animate-pulse"></div>
    </div>

    <div class="h-[550px] w-full bg-gray-50 dark:bg-gray-900/50 rounded-xl animate-pulse flex flex-col items-center justify-center space-y-4 px-6 overflow-hidden">
        <div class="grid grid-cols-7 gap-2 w-full h-full p-4">
            @for ($i = 0; $i < 70; $i++)
                <div class="bg-gray-200 dark:bg-gray-700 rounded-sm animate-pulse" style="opacity: {{ rand(10, 60) / 100 }}"></div>
            @endfor
        </div>
    </div>
</div>
