<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Permissions Management') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Manage roles and their permissions.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Roles List -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Roles') }}</h2>
                </div>
                <div class="p-2 space-y-1">
                    @foreach($roles as $role)
                        <button
                            wire:click="selectRole({{ $role->id }})"
                            @class([
                                'w-full text-left px-4 py-2 rounded-lg transition-colors',
                                'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400 font-medium' => $selectedRole && $selectedRole->id === $role->id,
                                'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50' => !$selectedRole || $selectedRole->id !== $role->id,
                            ])
                        >
                            {{ ucfirst($role->name) }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Permissions Grid -->
        <div class="lg:col-span-3">
            @if($selectedRole)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('Permissions for') }} <span class="text-primary-600 dark:text-primary-400">{{ ucfirst($selectedRole->name) }}</span>
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Select the permissions this role should have.') }}</p>
                    </div>

                    <div class="p-6 space-y-8">
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="space-y-4">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700 pb-2">
                                    {{ str_replace('_', ' ', $group) }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                                    @foreach($groupPermissions as $permission)
                                        <div class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <x-checkbox
                                                wire:click="togglePermission('{{ $permission['name'] }}')"
                                                :checked="in_array($permission['name'], $rolePermissions)"
                                                id="p-{{ $permission['id'] }}"
                                                lg
                                            />
                                            <label for="p-{{ $permission['id'] }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                                {{ explode(' ', $permission['name'])[0] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                    <x-icon name="shield-check" class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600" />
                    <p class="mt-4 text-gray-500 dark:text-gray-400">{{ __('Please select a role to manage its permissions.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
