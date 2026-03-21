<?php

namespace App\Livewire\Settings;

use App\Jobs\ImportLegacyDataJob;
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
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $path = $this->file->store('temp-imports');

        ImportLegacyDataJob::dispatch($path);

        $this->notification()->success(
            title: __('settings.import_started'),
            description: __('settings.import_started_description'),
        );

        $this->reset('file');
    }

    public function render()
    {
        return view('livewire.settings.import');
    }
}
