<?php

namespace App\Jobs;

use App\Enums\BannerJobStatus;
use App\Enums\Queue;
use App\Models\Banner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class ExportBannerJob implements ShouldQueue
{
    use Queueable;

    private const MM_PER_PIXEL = 25.4 / 96;

    public int $timeout = 300;

    public function __construct(public Banner $banner, public int $scale)
    {
        $this->onQueue(Queue::Default);
    }

    public function handle(): void
    {
        $this->banner->update(['export_status' => BannerJobStatus::Generating]);

        Storage::delete(array_filter([
            $this->banner->export_png_path,
            $this->banner->export_pdf_path,
        ]));

        $html = view('banners.render', [
            'banner'        => $this->banner,
            'backgroundSrc' => $this->backgroundSrc(),
            'logoSrc'       => $this->logoSrc(),
        ])->render();

        Storage::makeDirectory("banners/{$this->banner->id}");

        $width = $this->banner->format->width();
        $height = $this->banner->format->height();

        $pngPath = "banners/{$this->banner->id}/banner-{$this->scale}x.png";
        $this->browsershot($html, $width, $height)
            ->deviceScaleFactor($this->scale)
            ->setScreenshotType('png')
            ->save(Storage::path($pngPath));

        $pdfPath = "banners/{$this->banner->id}/banner.pdf";
        $this->browsershot($html, $width, $height)
            ->showBackground()
            ->paperSize($width * self::MM_PER_PIXEL, $height * self::MM_PER_PIXEL)
            ->save(Storage::path($pdfPath));

        $this->banner->update([
            'export_status'   => BannerJobStatus::Ready,
            'export_png_path' => $pngPath,
            'export_pdf_path' => $pdfPath,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        $this->banner->update(['export_status' => BannerJobStatus::Failed]);
    }

    private function backgroundSrc(): ?string
    {
        if ($this->banner->background_path === null) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode(Storage::get($this->banner->background_path));
    }

    private function logoSrc(): ?string
    {
        return Banner::logoDataUri();
    }

    private function browsershot(string $html, int $width, int $height): Browsershot
    {
        $browsershot = Browsershot::html($html)
            ->setNodeBinary(config('services.browsershot.node_binary'))
            ->setNpmBinary(config('services.browsershot.npm_binary'))
            ->setNodeModulePath(base_path('node_modules'))
            ->windowSize($width, $height)
            ->noSandbox()
            ->timeout(120)
            ->setDelay(2000);

        if ($chromePath = config('services.browsershot.chrome_path')) {
            $browsershot->setChromePath($chromePath);
        }

        return $browsershot;
    }
}
