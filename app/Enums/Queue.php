<?php

namespace App\Enums;

enum Queue: string
{
    case Default = 'default';
    case Low = 'low';
    case High = 'high';
}
