<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Login extends Component
{
    use WireUiActions;

    public string $email = '';
    public string $otp = '';
    public int $step = 1;

    protected $rules = [
        'email' => 'required|email|exists:users,email',
        'otp'   => 'required|string|min:6|max:6',
    ];

    public function sendOtp(): void
    {
        $this->validateOnly('email');

        $user = User::where('email', $this->email)->first();

        $user->sendOneTimePassword();

        $this->step = 2;

        $this->notification()->success(
            title: __('login.success'),
            description: __('login.otp_sent_message'),
        );
    }

    public function verifyOtp(): void
    {
        $this->validateOnly('otp');

        $user = User::where('email', $this->email)->first();

        if ($user->consumeOneTimePassword($this->otp)->isOk()) {
            Auth::login($user);

            $this->redirect(route('dashboard'));

            return;
        }

        $this->addError('otp', __('login.invalid_otp'));

        $this->notification()->error(
            title: __('login.error'),
            description: __('login.invalid_otp'),
        );
    }

    public function backToEmail(): void
    {
        $this->step = 1;
        $this->otp = '';
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.login');
    }
}
