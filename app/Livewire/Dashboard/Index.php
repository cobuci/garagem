<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class Index extends Component
{
    public function logout(): void
    {
        Auth::logout();

        $this->redirect(route('login'));
    }

    public function render(): View
    {
        return view('livewire.dashboard.index');
    }
}
