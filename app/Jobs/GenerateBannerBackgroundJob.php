<?php

namespace App\Jobs;

use App\Enums\BannerJobStatus;
use App\Enums\Queue;
use App\Models\Banner;
use App\Services\BannerBackgroundService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateBannerBackgroundJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 180;

    public function __construct(public Banner $banner)
    {
        $this->onQueue(Queue::Default);
    }

    public function handle(BannerBackgroundService $service): void
    {
        $this->banner->update(['background_status' => BannerJobStatus::Generating]);

        $previousPath = $this->banner->background_path;

        $path = $service->generate($this->banner);

        if ($previousPath) {
            Storage::delete($previousPath);
        }

        $this->banner->update([
            'background_status' => BannerJobStatus::Ready,
            'background_path'   => $path,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        $this->banner->update(['background_status' => BannerJobStatus::Failed]);
    }
}
