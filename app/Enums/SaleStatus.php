<?php

namespace App\Enums;

enum SaleStatus: string
{
    case Paid = 'paid';
    case Pending = 'pending';
    case Cancelled = 'cancelled';
}
