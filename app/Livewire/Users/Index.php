<?php

namespace App\Livewire\Users;

use App\Enums\Permission;
use App\Livewire\Forms\Users\UserForm;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use WireUi\Traits\WireUiActions;

/**
 * @property-read Collection $users
 * @property-read Collection $roles
 */
class Index extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public UserForm $form;

    public bool $showDrawer = false;

    public function mount(): void
    {
        $this->authorize(Permission::ViewUser->value);
    }

    #[Computed]
    public function users(): Collection
    {
        return User::with('roles')->get();
    }

    #[Computed]
    public function roles(): Collection
    {
        return Role::all();
    }

    public function create(): void
    {
        $this->form->reset();
        $this->form->user = null;
        $this->showDrawer = true;
    }

    public function edit(User $user): void
    {
        $this->form->setUser($user);
        $this->showDrawer = true;
    }

    public function save(): void
    {
        if ($this->form->user) {
            $this->authorize(Permission::EditUser->value);
            $this->form->update();
            $msg = __('admin.users.messages.updated');
        } else {
            $this->authorize(Permission::CreateUser->value);
            $this->form->store();
            $msg = __('admin.users.messages.created');
        }

        $this->notification()->success(
            title: __('admin.users.messages.success'),
            description: $msg,
        );

        $this->showDrawer = false;
    }

    public function updatedShowDrawer(bool $value): void
    {
        if (! $value) {
            $this->form->reset();
            $this->resetErrorBag();
        }
    }

    public function generatePassword(): void
    {
        $this->form->generatePassword();
    }

    public function delete(User $user): void
    {
        $this->authorize(Permission::DeleteUser->value);

        if ($user->id === auth()->id()) {
            $this->notification()->error(
                title: __('admin.users.messages.error'),
                description: __('admin.users.messages.cannot_delete_self'),
            );

            return;
        }

        $user->delete();

        $this->notification()->success(
            title: __('admin.users.messages.success'),
            description: __('admin.users.messages.deleted'),
        );
    }

    public function render(): View
    {
        return view('livewire.users.index');
    }
}
