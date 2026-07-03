<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Money = 'money';
    case CreditCard = 'credit_card';
    case DebitCard = 'debit_card';
    case Pix = 'pix';
    case Others = 'others';

    public function label(): string
    {
        return __('sales.payments.' . $this->value);
    }

    public static function fromRaw(string $value): self
    {
        return match (strtolower(trim($value))) {
            'money', 'cash', 'dinheiro' => self::Money,
            'credit_card', 'credito'    => self::CreditCard,
            'debit_card', 'debito'      => self::DebitCard,
            'pix'                       => self::Pix,
            default                     => self::Others,
        };
    }
}
