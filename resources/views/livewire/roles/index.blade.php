<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Permissions Management') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Manage roles and their permissions.') }}</p>
        </div>
        <x-button
            primary
            icon="plus"
            label="{{ __('New Role') }}"
            @click="$wire.set('showDrawer', true)"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Roles') }}</h2>
                </div>
                <div class="p-2 space-y-1">
                    @foreach($this->roles as $role)
                        <button
                            wire:click="selectRole({{ $role->id }})"
                            @class([
                                'w-full text-left px-4 py-2 rounded-lg transition-colors flex items-center justify-between group',
                                'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400 font-medium' => $selectedRoleId === $role->id,
                                'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50' => $selectedRoleId !== $role->id,
                            ])
                        >
                            <span>{{ ucfirst($role->name) }}</span>
                            @if($role->name === 'admin')
                                <x-icon name="lock-closed" class="w-4 h-4 text-gray-400" />
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            @if($selectedRoleId)
                @php
                    $selectedRole = $this->roles->firstWhere('id', $selectedRoleId);
                @endphp
                <div @class([
                    'bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden',
                    'opacity-75' => $selectedRole?->name === 'admin'
                ])>
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ __('Permissions for') }} <span class="text-primary-600 dark:text-primary-400">{{ ucfirst($selectedRole?->name) }}</span>
                                </h2>
                                @if($selectedRole?->name === 'admin')
                                    <x-badge flat gray icon="lock-closed" label="{{ __('Immutable') }}" />
                                @endif
                            </div>

                            @if($selectedRole?->name !== 'admin')
                                <x-button
                                    primary
                                    xs
                                    wire:click="savePermissions"
                                    label="{{ __('Save Changes') }}"
                                    wire:loading.attr="disabled"
                                />
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @if($selectedRole?->name === 'admin')
                                {{ __('The admin role always has all permissions and cannot be modified.') }}
                            @else
                                {{ __('Select the permissions this role should have.') }}
                            @endif
                        </p>
                    </div>

                    <div class="p-6 space-y-8">
                        @foreach($this->permissions as $group => $groupPermissions)
                            <div class="space-y-4">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700 pb-2">
                                    {{ str_replace('_', ' ', $group) }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                                    @foreach($groupPermissions as $permission)
                                        <div class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                             wire:key="role-{{ $selectedRoleId }}-perm-{{ $permission['id'] }}">
                                            <x-checkbox
                                                wire:model="rolePermissions"
                                                value="{{ $permission['name'] }}"
                                                :disabled="$selectedRole?->name === 'admin'"
                                                id="p-{{ $selectedRoleId }}-{{ $permission['id'] }}"
                                                lg
                                            />
                                            <label for="p-{{ $selectedRoleId }}-{{ $permission['id'] }}" @class([
                                                'text-sm font-medium cursor-pointer',
                                                'text-gray-700 dark:text-gray-300' => $selectedRole?->name !== 'admin',
                                                'text-gray-400 dark:text-gray-500 cursor-not-allowed' => $selectedRole?->name === 'admin'
                                            ])>
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

    <x-drawer wire:model="showDrawer" title="{{ __('Create New Role') }}" class="p-6">
        <form wire:submit="saveRole" class="space-y-6">
            <x-input
                wire:model="form.name"
                label="{{ __('Role Name') }}"
                placeholder="{{ __('Enter role name (e.g. Accountant)') }}"
                hint="{{ __('The role name will be used to identify it in the system.') }}"
            />

            <div class="flex justify-end gap-x-4">
                <x-button flat label="{{ __('Cancel') }}" x-on:click="close" />
                <x-button primary type="submit" label="{{ __('Create Role') }}" />
            </div>
        </form>
    </x-drawer>
</div>
