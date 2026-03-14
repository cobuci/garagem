<div class="space-y-6">
    <!-- Cabeçalho do Perfil -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('customers.index') }}" wire:navigate class="p-2 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                <x-icon name="arrow-left" class="w-5 h-5 text-gray-600" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h1>
                <p class="text-sm text-gray-500">{{ __('customers.profile_subtitle') }}</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <button class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition shadow-sm">
                {{ __('customers.edit') }}
            </button>
            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-500 transition shadow-sm">
                {{ __('orders.new_order') }}
            </button>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <x-icon name="banknotes" class="w-6 h-6 text-blue-600" />
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">{{ __('customers.total_spent') }}</h3>
            <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($this->totalSpent / 100, 2, ',', '.') }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-red-50 rounded-lg">
                    <x-icon name="credit-card" class="w-6 h-6 text-red-600" />
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">{{ __('customers.total_due') }}</h3>
            <p class="text-2xl font-bold text-red-600">R$ {{ number_format($this->totalDue / 100, 2, ',', '.') }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-50 rounded-lg">
                    <x-icon name="shopping-bag" class="w-6 h-6 text-purple-600" />
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">{{ __('customers.total_orders') }}</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $this->orders->count() }}</p>
        </div>
    </div>

    <!-- Histórico de Compras -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-900">{{ __('orders.history') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('orders.date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('orders.total') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('orders.paid') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('orders.status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('orders.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($this->orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">R$ {{ number_format($order->total_amount / 100, 2, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">R$ {{ number_format($order->paid_amount / 100, 2, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'paid' => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'partial' => 'bg-blue-100 text-blue-700',
                                    ];
                                    $statusLabels = [
                                        'paid' => __('orders.status_paid'),
                                        'pending' => __('orders.status_pending'),
                                        'partial' => __('orders.status_partial'),
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-indigo-600 hover:text-indigo-900">{{ __('orders.view_details') }}</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                {{ __('orders.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
