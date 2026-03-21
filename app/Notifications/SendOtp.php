<?php

namespace App\Notifications;

use App\Enums\Queue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\OneTimePasswords\Models\OneTimePassword;
use Spatie\OneTimePasswords\Notifications\OneTimePasswordNotification;

class SendOtp extends OneTimePasswordNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public OneTimePassword $oneTimePassword)
    {
        $this->onQueue(Queue::Default->value);
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('login.otp_subject'))
            ->markdown('mail.otp', [
                'otp' => $this->oneTimePassword->password,
            ]);
    }
}
