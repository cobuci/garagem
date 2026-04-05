<?php

namespace App\Livewire\Settings;

use App\Jobs\ImportLegacyDataJob;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;

class Import extends Component
{
    use WireUiActions;
    use WithFileUploads;

    public $file;

    public function save(): void
    {
        $this->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,sql', 'max:20480'],
        ]);

        $path = $this->file->store('temp-imports');

        ImportLegacyDataJob::dispatch($path);

        $this->notification()->success(
            title: __('settings.import_started'),
            description: __('settings.import_started_description'),
        );

        $this->reset('file');
    }

    public function render(): View
    {
        return view('livewire.settings.import');
    }
}
