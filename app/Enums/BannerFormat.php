<?php

namespace App\Enums;

enum BannerFormat: string
{
    case Stories = 'stories';
    case Post = 'post';
    case Square = 'square';

    public function width(): int
    {
        return 1080;
    }

    public function height(): int
    {
        return match ($this) {
            self::Stories => 1920,
            self::Post    => 1350,
            self::Square  => 1080,
        };
    }

    public function aspectRatio(): string
    {
        return match ($this) {
            self::Stories => '9:16',
            self::Post    => '4:5',
            self::Square  => '1:1',
        };
    }

    public function aiSize(): string
    {
        return match ($this) {
            self::Stories, self::Post => '2:3',
            self::Square              => '1:1',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Stories => __('banners.formats.stories'),
            self::Post    => __('banners.formats.post'),
            self::Square  => __('banners.formats.square'),
        };
    }
}
