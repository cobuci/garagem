<?php

use App\Notifications\SendOtp;
use Spatie\OneTimePasswords\Actions\ConsumeOneTimePasswordAction;
use Spatie\OneTimePasswords\Actions\CreateOneTimePasswordAction;
use Spatie\OneTimePasswords\Models\OneTimePassword;
use Spatie\OneTimePasswords\Support\OriginInspector\DefaultOriginEnforcer;
use Spatie\OneTimePasswords\Support\PasswordGenerators\NumericOneTimePasswordGenerator;

return [
    'default_expires_in_minutes' => 2,

    'only_one_active_one_time_password_per_user' => true,

    'enforce_same_origin' => true,

    'origin_enforcer' => DefaultOriginEnforcer::class,

    'password_generator' => NumericOneTimePasswordGenerator::class,

    'password_length' => 6,

    'redirect_successful_authentication_to' => '/dashboard',

    'rate_limit_attempts' => [
        'max_attempts_per_user'  => 5,
        'time_window_in_seconds' => 60,
    ],

    'model' => OneTimePassword::class,

    'notification' => SendOtp::class,

    'actions' => [
        'create_one_time_password'  => CreateOneTimePasswordAction::class,
        'consume_one_time_password' => ConsumeOneTimePasswordAction::class,
    ],
];
