<?php

namespace App\Enums;

enum BannerTheme: string
{
    case Barbecue = 'barbecue';
    case ColdDrinks = 'cold_drinks';
    case Snacks = 'snacks';
    case DailyPromo = 'daily_promo';
    case HappyHour = 'happy_hour';
    case BrandMinimal = 'brand_minimal';

    public function label(): string
    {
        return __("banners.themes.{$this->value}.label");
    }

    public function description(): string
    {
        return __("banners.themes.{$this->value}.description");
    }

    public function prompt(): string
    {
        return match ($this) {
            self::Barbecue     => 'Fundo de churrasco com brasas vermelhas e laranjas suaves, fumaça leve e desfocada, textura de madeira escura, clima de espetinho na churrasqueira, estilo fotográfico cinematográfico.',
            self::ColdDrinks   => 'Fundo com garrafas e copos gelados desfocados, gotas de condensação, gelo, tons de azul profundo e reflexos frios, clima refrescante de loja de conveniência.',
            self::Snacks       => 'Fundo com porções e petiscos desfocados ao longe, batata frita e salgados em tons quentes e dourados, clima aconchegante de bar.',
            self::DailyPromo   => 'Fundo escuro e elegante com gradiente suave, formas geométricas sutis e brilho discreto, visual limpo de promoção.',
            self::HappyHour    => 'Fundo de happy hour com luzes bokeh desfocadas, clima festivo noturno, tons âmbar e azuis, atmosfera de comemoração.',
            self::BrandMinimal => 'Fundo minimalista com gradiente entre azul céu e azul escuro, textura sutil, sem objetos, visual moderno e limpo.',
        };
    }
}
