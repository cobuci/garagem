<?php

namespace App\Livewire\Forms\Users;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public array $roles = [];

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->roles = $user->roles->pluck('name')->toArray();
        $this->password = '';
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user?->id)],
            'password' => [$this->user ? 'nullable' : 'required', 'string', 'min:8'],
            'roles'    => ['required', 'array', 'min:1'],
        ];
    }

    public function store(): User
    {
        $this->validate();

        $user = User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
        ]);

        $user->syncRoles($this->roles);

        $this->reset();

        return $user;
    }

    public function update(): void
    {
        $this->validate();

        $data = [
            'name'  => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        $this->user->update($data);
        $this->user->syncRoles($this->roles);

        $this->reset();
    }
}
