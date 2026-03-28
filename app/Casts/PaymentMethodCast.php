<?php

namespace App\Casts;

use App\Enums\PaymentMethod;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?PaymentMethod
    {
        if ($value === null) {
            return null;
        }

        return PaymentMethod::fromRaw($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof PaymentMethod) {
            return $value->value;
        }

        return PaymentMethod::fromRaw((string) $value)->value;
    }
}
