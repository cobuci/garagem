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
    case FathersDay = 'fathers_day';
    case MothersDay = 'mothers_day';
    case Christmas = 'christmas';
    case Easter = 'easter';
    case NewYear = 'new_year';
    case Valentines = 'valentines';

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
            self::FathersDay   => 'Fundo temático de Dia dos Pais, tons azuis e dourados suaves, textura elegante e acolhedora, detalhes sutis de gravata ou bigode desfocados nas bordas, clima carinhoso e celebrativo.',
            self::MothersDay   => 'Fundo temático de Dia das Mães, flores desfocadas e delicadas nas bordas, tons rosa, pêssego e creme, luz suave e aconchegante, clima afetivo.',
            self::Christmas    => 'Fundo natalino com luzes bokeh douradas e vermelhas, neve suave desfocada, tons verdes e vermelhos discretos, clima aconchegante de Natal, área central limpa.',
            self::Easter       => 'Fundo de Páscoa com tons pastel (lilás, amarelo claro e verde menta), ovos e flores desfocados nas bordas, luz clara e alegre, clima primaveril.',
            self::NewYear      => 'Fundo de Ano Novo com fogos de artifício desfocados e bokeh dourado, tons pretos, dourados e azul-noite, clima festivo e sofisticado.',
            self::Valentines   => 'Fundo de Dia dos Namorados com tons vermelho e rosa suaves, corações desfocados nas bordas, luz romântica e calorosa, clima carinhoso.',
        };
    }
}
