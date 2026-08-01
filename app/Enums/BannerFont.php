<?php

namespace App\Enums;

enum BannerFont: string
{
    case InstrumentSans = 'instrument_sans';
    case Anton = 'anton';
    case BebasNeue = 'bebas_neue';
    case Oswald = 'oswald';
    case Montserrat = 'montserrat';
    case PlayfairDisplay = 'playfair_display';
    case Pacifico = 'pacifico';

    public function label(): string
    {
        return match ($this) {
            self::InstrumentSans  => 'Instrument Sans',
            self::Anton           => 'Anton',
            self::BebasNeue       => 'Bebas Neue',
            self::Oswald          => 'Oswald',
            self::Montserrat      => 'Montserrat',
            self::PlayfairDisplay => 'Playfair Display',
            self::Pacifico        => 'Pacifico',
        };
    }

    public function family(): string
    {
        return match ($this) {
            self::PlayfairDisplay => "'Playfair Display', serif",
            self::Pacifico        => "'Pacifico', cursive",
            default               => "'{$this->label()}', sans-serif",
        };
    }

    public function googleQuery(): string
    {
        return match ($this) {
            self::InstrumentSans  => 'Instrument+Sans:wght@400;600;700;800',
            self::Oswald          => 'Oswald:wght@400;600;700',
            self::Montserrat      => 'Montserrat:wght@400;600;700;800',
            self::PlayfairDisplay => 'Playfair+Display:wght@400;600;700;800',
            default               => str_replace(' ', '+', $this->label()),
        };
    }

    public static function stylesheetUrl(self ...$fonts): string
    {
        $families = collect($fonts)
            ->unique()
            ->map(fn (self $font): string => 'family=' . $font->googleQuery())
            ->implode('&');

        return "https://fonts.googleapis.com/css2?{$families}&display=swap";
    }

    public static function fromDesign(array $design, string $key): self
    {
        return self::tryFrom($design[$key] ?? '') ?? self::InstrumentSans;
    }
}
