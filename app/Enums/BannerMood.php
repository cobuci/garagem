<?php

namespace App\Enums;

enum BannerMood: string
{
    case Dark = 'dark';
    case Light = 'light';
    case Night = 'night';

    public function label(): string
    {
        return __("banners.moods.{$this->value}");
    }

    public function prompt(): string
    {
        return match ($this) {
            self::Dark  => 'Iluminação escura e sofisticada.',
            self::Light => 'Iluminação clara e arejada.',
            self::Night => 'Ambiente noturno com pontos de luz.',
        };
    }
}
