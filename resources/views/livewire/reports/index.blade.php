<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <div class="flex-none bg-gray-50 dark:bg-gray-900 pb-2 w-full max-w-full">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('reports.title') }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('reports.subtitle') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <livewire:reports.sales-by-period lazy />
        <livewire:reports.sales-by-payment-method lazy />
    </div>
</div>
