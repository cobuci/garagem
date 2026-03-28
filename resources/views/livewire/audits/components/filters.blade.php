@use(App\Livewire\Audits\Index)
@props(['event', 'auditable', 'userId', 'users'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">

        <div class="flex items-center gap-2 flex-wrap">
            <button
                wire:click="filterByEvent('')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ $event === '' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                <x-icon name="list-bullet" class="w-4 h-4" />
                {{ __('audits.filters.all_events') }}
            </button>
            <button
                wire:click="filterByEvent('created')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ $event === 'created' ? 'bg-green-600 text-white' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                <x-icon name="plus-circle" class="w-4 h-4" />
                {{ __('audits.events.created') }}
            </button>
            <button
                wire:click="filterByEvent('updated')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ $event === 'updated' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                <x-icon name="pencil-square" class="w-4 h-4" />
                {{ __('audits.events.updated') }}
            </button>
            <button
                wire:click="filterByEvent('deleted')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ $event === 'deleted' ? 'bg-red-600 text-white' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                <x-icon name="trash" class="w-4 h-4" />
                {{ __('audits.events.deleted') }}
            </button>
            <button
                wire:click="filterByEvent('restored')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ $event === 'restored' ? 'bg-yellow-500 text-white' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                <x-icon name="arrow-uturn-left" class="w-4 h-4" />
                {{ __('audits.events.restored') }}
            </button>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 sm:ml-auto w-full sm:w-auto">
            <select
                wire:model.live="auditable"
                class="w-full sm:w-44 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
                <option value="">{{ __('audits.filters.all_models') }}</option>
                @foreach(array_keys(Index::AUDITABLE_MODELS) as $model)
                    <option value="{{ $model }}">{{ $model }}</option>
                @endforeach
            </select>

            <select
                wire:model.live="userId"
                class="w-full sm:w-44 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
                <option value="">{{ __('audits.filters.all_users') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
