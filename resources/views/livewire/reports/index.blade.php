<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <div class="flex-none bg-gray-50 dark:bg-gray-900 pb-2 w-full max-w-full">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('reports.title') }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('reports.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <livewire:reports.export-report />
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 mt-4">
            <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
                <button
                    wire:click="setActiveTab('overview')"
                    class="flex-1 min-w-[120px] py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'overview' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="chart-bar" class="inline-block w-5 h-5 mr-2" />
                    {{ __('reports.tabs.overview') }}
                </button>
                <button
                    wire:click="setActiveTab('products')"
                    class="flex-1 min-w-[120px] py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'products' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="cube" class="inline-block w-5 h-5 mr-2" />
                    {{ __('reports.tabs.products') }}
                </button>
                <button
                    wire:click="setActiveTab('customers')"
                    class="flex-1 min-w-[120px] py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'customers' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="users" class="inline-block w-5 h-5 mr-2" />
                    {{ __('reports.tabs.customers') }}
                </button>
                <button
                    wire:click="setActiveTab('inventory')"
                    class="flex-1 min-w-[120px] py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'inventory' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="archive-box" class="inline-block w-5 h-5 mr-2" />
                    {{ __('reports.tabs.inventory') }}
                </button>
            </nav>
        </div>
    </div>

    <div class="mt-6">
        @if($activeTab === 'overview')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <livewire:reports.sales-by-period lazy />
                <livewire:reports.sales-by-payment-method lazy />
                <livewire:reports.average-ticket-evolution lazy />
                <livewire:reports.sales-by-hour-and-day lazy />
            </div>
        @endif

        @if($activeTab === 'products')
            <div class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <livewire:reports.top-products lazy />
                    <livewire:reports.most-profitable-products lazy />
                </div>
                <div class="w-full">
                    <livewire:reports.profit-by-category lazy />
                </div>
            </div>
        @endif

        @if($activeTab === 'customers')
            <div class="w-full">
                <livewire:reports.churn-risk-customers lazy />
            </div>
        @endif

        @if($activeTab === 'inventory')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <livewire:reports.stock-turnover lazy />
                @if(class_exists(\App\Livewire\Reports\LowStockProducts::class))
                    <livewire:reports.low-stock-products lazy />
                @endif
            </div>
        @endif
    </div>
</div>
