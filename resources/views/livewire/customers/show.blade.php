@use(App\Enums\SaleStatus)
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('customers.index') }}" wire:navigate
               class="p-2 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                <x-icon name="arrow-left" class="w-5 h-5 text-gray-600"/>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('customers.profile_subtitle') }}</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <button wire:click="$dispatch('edit:customer', { customer: {{ $customer->id }} }   )"
                    class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition shadow-sm">
                {{ __('customers.edit') }}
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <x-icon name="banknotes" class="w-6 h-6 text-blue-600 dark:text-blue-400"/>
                </div>
            </div>
            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">{{ __('customers.total_spent') }}</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                R$ {{ number_format($this->totalSpent, 2, ',', '.') }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-red-50 dark:bg-red-900/30 rounded-lg">
                    <x-icon name="credit-card" class="w-6 h-6 text-red-600 dark:text-red-400"/>
                </div>
            </div>
            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">{{ __('customers.total_due') }}</h3>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                R$ {{ number_format($this->totalDue, 2, ',', '.') }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                    <x-icon name="shopping-bag" class="w-6 h-6 text-purple-600 dark:text-purple-400"/>
                </div>
            </div>
            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">{{ __('customers.total_orders') }}</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->orders->count() }}</p>
        </div>
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div
            class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 dark:text-white">{{ __('orders.history') }}</h3>
            <div class="flex bg-gray-100 dark:bg-gray-800 p-1 rounded-lg">
                <button
                    wire:click="filterByStatus(null)"
                    class="px-3 py-1 text-xs font-medium rounded-md transition {{ is_null($status) ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    {{ __('sales.all_sales') }}
                </button>
                <button
                    wire:click="filterByStatus('paid')"
                    class="px-3 py-1 text-xs font-medium rounded-md transition {{ $status === 'paid' ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    {{ __('sales.paid_sales') }}
                </button>
                <button
                    wire:click="filterByStatus('pending')"
                    class="px-3 py-1 text-xs font-medium rounded-md transition {{ $status === 'pending' ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    {{ __('sales.pending_sales') }}
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('orders.date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('orders.total') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('orders.status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('orders.actions') }}</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($this->orders as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div
                                class="text-sm text-gray-900 dark:text-white">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                R$ {{ number_format($order->total_amount, 2, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClasses = [
                                    SaleStatus::Paid->value => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    SaleStatus::Pending->value => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    SaleStatus::Cancelled->value => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                ];
                                $statusLabels = [
                                    SaleStatus::Paid->value => __('sales.paid'),
                                    SaleStatus::Pending->value => __('sales.pending'),
                                    SaleStatus::Cancelled->value => __('sales.cancel'),
                                ];
                            @endphp
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$order->status->value] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-400' }}">
                                    {{ $statusLabels[$order->status->value] ?? $order->status->value }}
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <button wire:click="showDetails({{ $order->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <x-icon name="eye" class="w-5 h-5"/>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('orders.empty') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($this->orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $this->orders->links() }}
            </div>
        @endif
    </div>

    @include('livewire.sales.components.details-modal', ['selectedSale' => $this->selectedSale])
    @include('livewire.sales.components.confirm-payment-modal', ['selectedSale' => $this->selectedSale])
    @include('livewire.sales.components.confirm-cancel-modal', ['selectedSale' => $this->selectedSale])

    <div>
        <livewire:customers.edit/>
    </div>
</div>
