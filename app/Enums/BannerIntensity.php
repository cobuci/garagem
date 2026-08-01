<?php

namespace App\Enums;

enum BannerIntensity: string
{
    case Soft = 'soft';
    case Bold = 'bold';

    public function label(): string
    {
        return __("banners.intensities.{$this->value}");
    }

    public function prompt(): string
    {
        return match ($this) {
            self::Soft => 'Elementos suaves, discretos e bem desfocados.',
            self::Bold => 'Cores vivas e elementos marcantes nas bordas.',
        };
    }
}
