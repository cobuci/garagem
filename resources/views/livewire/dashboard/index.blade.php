<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('dashboard.title') }}</h1>
        <p class="text-gray-600">{{ __('dashboard.welcome_back', ['name' => auth()->user()->name ?? auth()->user()->email]) }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <x-icon name="shopping-cart" class="w-6 h-6 text-blue-600" />
                </div>
                <span class="text-green-500 text-sm font-medium">+12%</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">{{ __('dashboard.sales_today') }}</h3>
            <p class="text-2xl font-bold text-gray-900">R$ 1.250,00</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-50 rounded-lg">
                    <x-icon name="users" class="w-6 h-6 text-purple-600" />
                </div>
                <span class="text-green-500 text-sm font-medium">+5%</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">{{ __('dashboard.new_customers') }}</h3>
            <p class="text-2xl font-bold text-gray-900">24</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-orange-50 rounded-lg">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-orange-600" />
                </div>
                <span class="text-red-500 text-sm font-medium">-2%</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">{{ __('dashboard.conversion_rate') }}</h3>
            <p class="text-2xl font-bold text-gray-900">3.2%</p>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">{{ __('dashboard.recent_activities') }}</h3>
            <button class="text-sm text-indigo-600 font-medium hover:text-indigo-500">{{ __('dashboard.view_all') }}</button>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach(range(1, 5) as $i)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                            <x-icon name="user" class="w-5 h-5 text-gray-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('dashboard.new_order', ['id' => '1234' . $i]) }}</p>
                            <p class="text-xs text-gray-500">{{ __('dashboard.minutes_ago', ['minutes' => $i * 10]) }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-900">R$ {{ rand(100, 500) }},00</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
