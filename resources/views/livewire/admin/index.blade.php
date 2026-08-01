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
            <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
                <button
                    wire:click="setActiveTab('users')"
                    class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'users' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="users" class="inline-block w-5 h-5 mr-2" />
                    {{ __('admin.tabs.users') }}
                </button>
                <button
                    wire:click="setActiveTab('import')"
                    class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'import' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="arrow-down-tray" class="inline-block w-5 h-5 mr-2" />
                    {{ __('admin.tabs.import') }}
                </button>
                <button
                    wire:click="setActiveTab('roles')"
                    class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'roles' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="shield-check" class="inline-block w-5 h-5 mr-2" />
                    {{ __('admin.tabs.roles') }}
                </button>
                <button
                    wire:click="setActiveTab('audits')"
                    class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'audits' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="clipboard-document-list" class="inline-block w-5 h-5 mr-2" />
                    {{ __('admin.tabs.audits') }}
                </button>
                <button
                    wire:click="setActiveTab('brand')"
                    class="py-4 px-4 text-center border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'brand' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="photo" class="inline-block w-5 h-5 mr-2" />
                    {{ __('admin.tabs.brand') }}
                </button>
                @can(\App\Enums\Permission::ManageChangelog->value)
                    <button
                        wire:click="setActiveTab('changelog')"
                        class="py-4 px-4 text-center border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'changelog' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                    >
                        <x-icon name="megaphone" class="inline-block w-5 h-5 mr-2" />
                        {{ __('admin.tabs.changelog') }}
                    </button>
                @endcan
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

            @if($activeTab === 'audits')
                <livewire:audits.index />
            @endif

            @if($activeTab === 'brand')
                <livewire:admin.brand-logo />
            @endif

            @if($activeTab === 'changelog')
                <livewire:changelog.index />
            @endif
        </div>
    </div>
</div>
