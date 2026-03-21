<?php

namespace App\Jobs;

use App\Actions\Reports\GetSystemReportData;
use App\Enums\Queue;
use App\Mail\SystemReportMail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenerateSystemReportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $startDate,
        public string $endDate,
    ) {
        $this->onQueue(Queue::Default);
    }

    public function handle(GetSystemReportData $reportDataAction): void
    {
        $data = $reportDataAction->execute($this->startDate, $this->endDate);

        $pdf = Pdf::loadView('pdf.system-report', [
            'user' => $this->user,
            ...$data,
        ]);

        $fileName = "reports/system_report_{$this->user->id}_" . now()->timestamp . '.pdf';
        $pdfPath = storage_path("app/public/{$fileName}");

        Storage::disk('public')->put($fileName, $pdf->output());

        Mail::to($this->user->email)->send(new SystemReportMail(
            $this->user->name,
            $this->startDate,
            $this->endDate,
            $pdfPath,
        ));
    }
}
