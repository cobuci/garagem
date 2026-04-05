<?php

namespace App\Livewire\Roles;

use App\Enums\Permission as PermissionEnum;
use App\Livewire\Forms\Roles\RoleForm;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use WireUi\Traits\WireUiActions;

/**
 * @property-read Collection $roles
 */
class Index extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public RoleForm $form;

    public $selectedRoleId;

    public $rolePermissions = [];

    public $showDrawer = false;

    public function mount(): void
    {
        $this->authorize(PermissionEnum::ViewRole->value);

        if ($this->roles->count() > 0) {
            $this->selectRole($this->roles->first()->id);
        }
    }

    #[Computed]
    public function roles(): Collection
    {
        return Role::all();
    }

    #[Computed]
    public function permissions(): array
    {
        return Permission::all()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);

            return end($parts);
        })->all();
    }

    public function selectRole(int $roleId): void
    {
        $this->selectedRoleId = $roleId;
        $role = Role::findById($roleId);
        $this->rolePermissions = $role->permissions->pluck('name')->toArray();
        $this->resetErrorBag();
    }

    public function savePermissions(): void
    {
        $this->authorize(PermissionEnum::EditRole->value);

        $role = Role::findById($this->selectedRoleId);

        if ($role->name === 'admin') {
            $this->notification()->error(
                title: __('Ação não permitida'),
                description: __('A role Admin é imutável.'),
            );

            return;
        }

        $role->syncPermissions($this->rolePermissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->notification()->success(
            title: __('Roles updated'),
            description: __('Permissions updated successfully.'),
        );
    }

    public function saveRole(): void
    {
        $this->authorize(PermissionEnum::CreateRole->value);

        $role = $this->form->store();

        $this->showDrawer = false;

        $this->selectRole($role->id);

        $this->notification()->success(
            title: __('Role created'),
            description: __('New role created successfully.'),
        );
    }

    public function updatedShowDrawer(bool $value): void
    {
        if (! $value) {
            $this->form->reset();
            $this->resetErrorBag();
        }
    }

    public function render(): View
    {
        return view('livewire.roles.index');
    }
}
