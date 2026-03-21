<?php

namespace App\Livewire\Roles;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use WireUi\Traits\WireUiActions;

#[Layout('layouts.app')]
class Index extends Component
{
    use WireUiActions;

    public $roles;

    public $permissions;

    public $selectedRole;

    public $rolePermissions = [];

    public function mount(): void
    {
        $this->roles = Role::all();
        $this->permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);

            return $parts[count($parts) - 1];
        })->all();

        if ($this->roles->count() > 0) {
            $this->selectRole($this->roles->first()->id);
        }
    }

    public function selectRole(int $roleId): void
    {
        $this->selectedRole = Role::find($roleId);
        $this->rolePermissions = $this->selectedRole->permissions->pluck('name')->all();
    }

    public function togglePermission(string $permissionName): void
    {
        if (in_array($permissionName, $this->rolePermissions)) {
            $this->selectedRole->revokePermissionTo($permissionName);
            $this->rolePermissions = array_diff($this->rolePermissions, [$permissionName]);
        } else {
            $this->selectedRole->givePermissionTo($permissionName);
            $this->rolePermissions[] = $permissionName;
        }

        $this->notification()->success(
            title: __('Roles updated'),
            description: __('Permission updated successfully.'),
        );
    }

    public function render(): View
    {
        return view('livewire.roles.index');
    }
}
