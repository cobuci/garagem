<?php

use App\Actions\Reports\GetSystemReportData;
use App\Jobs\GenerateSystemReportJob;
use App\Mail\SystemReportMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

it('generates a report on private disk and deletes it after sending email', function () {
    Storage::fake();
    Mail::fake();

    $user = User::factory()->create();
    $job = new GenerateSystemReportJob($user, '2024-01-01', '2024-01-31');

    $job->handle(new GetSystemReportData);

    Mail::assertSent(SystemReportMail::class);

    $files = Storage::disk('local')->allFiles('reports');
    expect($files)->toBeEmpty();
});
