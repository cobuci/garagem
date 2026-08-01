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
        $prompt = $this->prompt($banner);

        $banner->update(['background_prompt' => $prompt]);

        $image = Image::of($prompt)
            ->size($banner->format->aiSize())
            ->quality('low')
            ->timeout(120)
            ->generate(
                provider: Lab::OpenAI,
                model: config('ai.providers.openai.models.image.default'),
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
        return collect([
            'Crie uma imagem de fundo para um banner promocional de uma loja de conveniência.',
            $banner->background_theme?->prompt(),
            $banner->background_mood?->prompt(),
            $banner->background_intensity?->prompt(),
            'A imagem deve ser apenas um fundo decorativo, sem nenhum texto, letra, número ou logotipo.',
            'Deixe a área central mais limpa e com bom contraste para receber texto por cima.',
        ])->filter()->implode(' ');
    }
}
