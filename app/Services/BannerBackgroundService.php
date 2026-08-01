<?php

namespace App\Services;

use App\Models\Banner;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Image;
use RuntimeException;

class BannerBackgroundService
{
    public function generate(Banner $banner): string
    {
        $image = Image::of($this->prompt($banner))
            ->size($banner->format->aspectRatio())
            ->quality('low')
            ->timeout(120)
            ->generate(
                provider: Lab::Gemini,
                model: config('ai.providers.gemini.models.image.default'),
            );

        $path = $image->storeAs(
            "banners/{$banner->id}",
            'background-' . now()->timestamp . '.png',
        );

        throw_if($path === false, new RuntimeException('Falha ao salvar a imagem gerada.'));

        return $path;
    }

    private function prompt(Banner $banner): string
    {
        return implode(' ', [
            'Crie uma imagem de fundo para um banner promocional.',
            'A imagem deve ser apenas um fundo decorativo, sem nenhum texto, letra, número ou logotipo.',
            'Deixe a área central mais limpa e com bom contraste para receber texto por cima.',
            'Estilo desejado: ' . $banner->background_prompt,
        ]);
    }
}
