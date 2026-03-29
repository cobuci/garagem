<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\SendOtpRequest;
use App\Http\Requests\Api\V1\Auth\VerifyOtpRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class OtpController extends Controller
{
    public function send(SendOtpRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->where('email', $request->email)->firstOrFail();

        $user->sendOneTimePassword();

        return $this->accepted('OTP sent successfully.');
    }

    public function verify(VerifyOtpRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->where('email', $request->email)->firstOrFail();

        $result = $user->consumeOneTimePassword($request->code);

        if (! $result->isOk()) {
            return $this->unprocessable('Invalid or expired OTP.');
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return $this->ok('Authenticated successfully.', [
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }
}
