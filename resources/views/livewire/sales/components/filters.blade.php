@props(['status'])

<div class="flex items-center border-b border-gray-200 dark:border-gray-700 overflow-x-auto w-full max-w-full">
    <div class="flex gap-8">
        <button
            wire:click="filterByStatus('pending')"
            class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200 flex items-center gap-2 {{ $status === 'pending' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <x-icon name="clock" class="w-4 h-4"/>
            {{ __('sales.pending_sales') }}
        </button>
        <button
            wire:click="filterByStatus('paid')"
            class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200 flex items-center gap-2 {{ $status === 'paid' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <x-icon name="check-circle" class="w-4 h-4"/>
            {{ __('sales.paid_sales') }}
        </button>
    </div>
</div>
