<?php

namespace App\Enums;

enum MobileSaleStatus: string
{
    case Pending = 'pending';
    case Synced = 'synced';
    case Failed = 'failed';
}
