<?php

namespace App\Livewire\Forms\Roles;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Spatie\Permission\Models\Role;

class RoleForm extends Form
{
    #[Validate(['required', 'string', 'max:255', 'unique:roles,name'])]
    public string $name = '';

    public function store(): Role
    {
        $this->validate();

        $role = Role::create(['name' => $this->name]);

        $this->reset();

        return $role;
    }
}
