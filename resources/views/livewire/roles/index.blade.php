<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-primary-100 dark:bg-primary-900/40 rounded-lg">
                <x-icon name="shield-check" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">{{ __('Roles & Permissions') }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Define what each role can see and do in the system.') }}</p>
            </div>
        </div>
        <x-button
            primary
            icon="plus"
            label="{{ __('Create New Role') }}"
            @click="$wire.set('showDrawer', true)"
            class="shadow-sm"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <h2 class="font-semibold text-gray-900 dark:text-white flex items-center">
                        <x-icon name="user-group" class="w-4 h-4 mr-2 text-gray-400" />
                        {{ __('Available Roles') }}
                    </h2>
                </div>
                <div class="p-2 space-y-1">
                    @foreach($this->roles as $role)
                        <button
                            wire:click="selectRole({{ $role->id }})"
                            @class([
                                'w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center justify-between group',
                                'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400 font-semibold shadow-xs' => $selectedRoleId === $role->id,
                                'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' => $selectedRoleId !== $role->id,
                            ])
                        >
                            <span class="truncate">{{ ucfirst($role->name) }}</span>
                            <div class="flex items-center">
                                @if($role->name === 'admin')
                                    <x-icon name="lock-closed" class="w-4 h-4 text-gray-400 group-hover:text-gray-500 transition-colors" />
                                @else
                                    <x-icon name="chevron-right" @class([
                                        'w-4 h-4 transition-all duration-200',
                                        'opacity-100 translate-x-0' => $selectedRoleId === $role->id,
                                        'opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0' => $selectedRoleId !== $role->id,
                                    ]) />
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-9">
            @if($selectedRoleId)
                @php
                    $selectedRole = $this->roles->firstWhere('id', $selectedRoleId);
                @endphp
                <div @class([
                    'bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300',
                    'ring-2 ring-primary-500/10' => $selectedRole?->name !== 'admin'
                ])>
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800/30">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-3">
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ __('Permissions for') }} <span class="text-primary-600 dark:text-primary-400">{{ ucfirst($selectedRole?->name) }}</span>
                                    </h2>
                                    @if($selectedRole?->name === 'admin')
                                        <x-badge flat gray icon="lock-closed" label="{{ __('System Protected') }}" class="font-semibold uppercase tracking-wider text-[10px]" />
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($selectedRole?->name === 'admin')
                                        {{ __('The administrator role has full access to all system features by default.') }}
                                    @else
                                        {{ __('Customize access levels by selecting the specific permissions below.') }}
                                    @endif
                                </p>
                            </div>

                            @if($selectedRole?->name !== 'admin')
                                <div class="flex items-center space-x-3">
                                    <x-button
                                        primary
                                        icon="check"
                                        wire:click="savePermissions"
                                        label="{{ __('Save Permissions') }}"
                                        wire:loading.attr="disabled"
                                        class="w-full sm:w-auto shadow-sm"
                                    />
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach($this->permissions as $group => $groupPermissions)
                                <div class="space-y-4 bg-gray-50/50 dark:bg-gray-800/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                                        <h3 class="text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 flex items-center">
                                            <span class="w-2 h-2 bg-primary-500 rounded-full mr-2"></span>
                                            {{ str_replace('_', ' ', $group) }}
                                        </h3>
                                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">
                                            {{ count($groupPermissions) }} {{ __('Perms') }}
                                        </span>
                                    </div>

                                    <div class="space-y-2.5">
                                        @foreach($groupPermissions as $permission)
                                            @php
                                                $permName = explode(' ', $permission['name'])[0];
                                                $isAction = in_array($permName, ['Create', 'Edit', 'Delete', 'Update', 'Destroy', 'Cancel', 'Mark', 'Finish']);
                                                $isDangerous = in_array($permName, ['Delete', 'Destroy', 'Cancel']);
                                            @endphp
                                            <div @class([
                                                'flex items-center justify-between p-3 rounded-xl border transition-all duration-200 group/item',
                                                'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-800 hover:shadow-sm' => $selectedRole?->name !== 'admin',
                                                'bg-gray-50/50 dark:bg-gray-700/30 border-gray-200 dark:border-gray-600' => $selectedRole?->name === 'admin'
                                            ])
                                                 wire:key="role-{{ $selectedRoleId }}-perm-{{ $permission['id'] }}">
                                                <div class="flex items-center space-x-3">
                                                    <div @class([
                                                        'w-8 h-8 rounded-lg flex items-center justify-center transition-colors',
                                                        'bg-gray-50 dark:bg-gray-900 text-gray-400 group-hover/item:text-primary-500 group-hover/item:bg-primary-50 dark:group-hover/item:bg-primary-900/30' => $selectedRole?->name !== 'admin',
                                                        'bg-gray-100 dark:bg-gray-900 text-gray-400 dark:text-gray-500' => $selectedRole?->name === 'admin'
                                                    ])>
                                                        @php
                                                            $icon = match($permName) {
                                                                'View' => 'eye',
                                                                'Create' => 'plus-circle',
                                                                'Edit' => 'pencil-alt',
                                                                'Delete', 'Destroy' => 'trash',
                                                                'Cancel' => 'x-circle',
                                                                'Mark' => 'check-circle',
                                                                'Purchase' => 'shopping-cart',
                                                                default => 'shield-check'
                                                            };
                                                        @endphp
                                                        <x-icon :name="$icon" class="w-4 h-4" />
                                                    </div>
                                                    <label for="p-{{ $selectedRoleId }}-{{ $permission['id'] }}" @class([
                                                        'text-sm font-medium transition-colors',
                                                        'text-gray-700 dark:text-gray-200 group-hover/item:text-gray-900 dark:group-hover/item:text-white cursor-pointer' => $selectedRole?->name !== 'admin',
                                                        'text-gray-400 dark:text-gray-500 cursor-not-allowed' => $selectedRole?->name === 'admin'
                                                    ])>
                                                        {{ $permName }}
                                                    </label>
                                                </div>

                                                <x-checkbox
                                                    wire:model="rolePermissions"
                                                    value="{{ $permission['name'] }}"
                                                    :disabled="$selectedRole?->name === 'admin'"
                                                    id="p-{{ $selectedRoleId }}-{{ $permission['id'] }}"
                                                    md
                                                    :primary="!$isDangerous"
                                                    :negative="$isDangerous"
                                                    class="rounded-lg"
                                                />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-dashed border-gray-300 dark:border-gray-700 p-20 text-center">
                    <div class="w-20 h-20 mx-auto bg-gray-50 dark:bg-gray-900/50 rounded-full flex items-center justify-center mb-6">
                        <x-icon name="user-group" class="w-10 h-10 text-gray-300 dark:text-gray-600" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('No Role Selected') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">{{ __('Please select a role from the sidebar to manage its access levels and permissions.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <x-drawer wire:model="showDrawer" title="{{ __('Create New Role') }}" right lg>
        <form wire:submit="saveRole" class="space-y-6">
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Define a name for the new role. You can customize its permissions after creation.') }}
                </p>

                <x-input
                    wire:model="form.name"
                    label="{{ __('Role Name') }}"
                    placeholder="{{ __('e.g. Sales Associate') }}"
                    hint="{{ __('Use descriptive names for your team roles.') }}"
                    icon="identification"
                    class="rounded-xl"
                />
            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                <x-button flat label="{{ __('Cancel') }}" @click="show = false" class="rounded-xl" />
                <x-button primary type="submit" label="{{ __('Create Role') }}" icon="plus" class="rounded-xl shadow-sm" />
            </div>
        </form>
    </x-drawer>
</div>
