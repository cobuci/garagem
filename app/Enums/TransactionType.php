<?php

namespace App\Enums;

enum TransactionType: string
{
    case Sale = 'sale';
    case Purchase = 'purchase';
    case CancelledSale = 'cancelled_sale';
    case ManualAdjustment = 'manual_adjustment';

    public function label(): string
    {
        return match ($this) {
            self::Sale             => __('finance.transaction_type.sale'),
            self::Purchase         => __('finance.transaction_type.purchase'),
            self::CancelledSale    => __('finance.transaction_type.cancelled_sale'),
            self::ManualAdjustment => __('finance.transaction_type.manual_adjustment'),
        };
    }
}
