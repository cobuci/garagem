<?php

namespace App\Services;

use App\Models\Banner;
use Gemini\Data\GenerationConfig;
use Gemini\Data\ImageConfig;
use Gemini\Enums\ResponseModality;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class BannerBackgroundService
{
    public function generate(Banner $banner): string
    {
        $generationConfig = new GenerationConfig(
            responseMimeType: null,
            responseModalities: [ResponseModality::IMAGE, ResponseModality::TEXT],
            imageConfig: new ImageConfig(aspectRatio: $banner->format->aspectRatio()),
        );

        $response = Gemini::generativeModel(model: config('gemini.banner_image_model'))
            ->withGenerationConfig($generationConfig)
            ->generateContent($this->prompt($banner));

        $image = collect($response->parts())
            ->first(fn ($part) => $part->inlineData !== null);

        throw_if($image === null, new RuntimeException('Gemini não retornou uma imagem.'));

        $path = "banners/{$banner->id}/background-" . now()->timestamp . '.png';
        Storage::put($path, base64_decode($image->inlineData->data));

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
