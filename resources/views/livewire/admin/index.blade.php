<div class="flex flex-col space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('admin.title') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('admin.subtitle') }}
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button
                    wire:click="setActiveTab('users')"
                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'users' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="users" class="inline-block w-5 h-5 mr-2" />
                    {{ __('admin.tabs.users') }}
                </button>
                <button
                    wire:click="setActiveTab('import')"
                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'import' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="arrow-down-tray" class="inline-block w-5 h-5 mr-2" />
                    {{ __('admin.tabs.import') }}
                </button>
                <button
                    wire:click="setActiveTab('roles')"
                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'roles' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="shield-check" class="inline-block w-5 h-5 mr-2" />
                    {{ __('admin.tabs.roles') }}
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($activeTab === 'users')
                <livewire:users.index />
            @endif

            @if($activeTab === 'import')
                <livewire:settings.import />
            @endif

            @if($activeTab === 'roles')
                <livewire:roles.index />
            @endif
        </div>
    </div>
</div>
