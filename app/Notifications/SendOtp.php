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
            ->subject('Your One-Time Password')
            ->line('Your one-time password is: ' . $this->oneTimePassword->password)
            ->line('This password will expire in 10 minutes.')
            ->line('If you did not request this, please ignore this email.');
    }
}
