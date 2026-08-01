<?php

namespace App\Enums;

enum BannerJobStatus: string
{
    case None = 'none';
    case Generating = 'generating';
    case Ready = 'ready';
    case Failed = 'failed';
}
