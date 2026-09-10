<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <div class="flex-none pb-2 w-full max-w-full">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('reports.title') }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('reports.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <livewire:reports.export-report />
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700/80 mt-4">
            <nav class="flex -mb-px overflow-x-auto" aria-label="{{ __('reports.title') }}" role="tablist">
                <button
                    type="button"
                    role="tab"
                    id="tab-overview"
                    aria-selected="{{ $activeTab === 'overview' ? 'true' : 'false' }}"
                    wire:click="setActiveTab('overview')"
                    class="flex-1 min-w-[130px] py-3.5 px-4 text-center border-b-2 text-sm transition-all duration-200 whitespace-nowrap inline-flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-inset {{ $activeTab === 'overview' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 font-medium' }}"
                >
                    <x-icon name="chart-bar" class="w-5 h-5 shrink-0" />
                    <span>{{ __('reports.tabs.overview') }}</span>
                </button>
                <button
                    type="button"
                    role="tab"
                    id="tab-products"
                    aria-selected="{{ $activeTab === 'products' ? 'true' : 'false' }}"
                    wire:click="setActiveTab('products')"
                    class="flex-1 min-w-[130px] py-3.5 px-4 text-center border-b-2 text-sm transition-all duration-200 whitespace-nowrap inline-flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-inset {{ $activeTab === 'products' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 font-medium' }}"
                >
                    <x-icon name="cube" class="w-5 h-5 shrink-0" />
                    <span>{{ __('reports.tabs.products') }}</span>
                </button>
                <button
                    type="button"
                    role="tab"
                    id="tab-customers"
                    aria-selected="{{ $activeTab === 'customers' ? 'true' : 'false' }}"
                    wire:click="setActiveTab('customers')"
                    class="flex-1 min-w-[130px] py-3.5 px-4 text-center border-b-2 text-sm transition-all duration-200 whitespace-nowrap inline-flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-inset {{ $activeTab === 'customers' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 font-medium' }}"
                >
                    <x-icon name="users" class="w-5 h-5 shrink-0" />
                    <span>{{ __('reports.tabs.customers') }}</span>
                </button>
                <button
                    type="button"
                    role="tab"
                    id="tab-inventory"
                    aria-selected="{{ $activeTab === 'inventory' ? 'true' : 'false' }}"
                    wire:click="setActiveTab('inventory')"
                    class="flex-1 min-w-[130px] py-3.5 px-4 text-center border-b-2 text-sm transition-all duration-200 whitespace-nowrap inline-flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-inset {{ $activeTab === 'inventory' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 font-medium' }}"
                >
                    <x-icon name="archive-box" class="w-5 h-5 shrink-0" />
                    <span>{{ __('reports.tabs.inventory') }}</span>
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
            <div class="w-full space-y-8">
                <livewire:reports.stock-turnover lazy />
                @if(class_exists(\App\Livewire\Reports\LowStockProducts::class))
                    <livewire:reports.low-stock-products lazy />
                @endif
            </div>
        @endif
    </div>
</div>
