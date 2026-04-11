<?php

namespace App\Livewire\Changelog;

use App\Enums\Permission;
use App\Models\Changelog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

/**
 * @property-read Collection<int, Changelog> $changelogs
 */
class Index extends Component
{
    use AuthorizesRequests;
    use WireUiActions;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $selectedSeeder = '';

    /** @var array<string, string> */
    public const SEEDERS = [
        'ChangelogV100Seeder' => 'v1.0.0 — Novas Métricas, Abas em Relatórios e Melhorias',
        'ChangelogV110Seeder' => 'v1.1.0 — Novo Dashboard e Métricas de Vendas',
    ];

    public function mount(): void
    {
        $this->authorize(Permission::ManageChangelog->value);
    }

    #[Computed]
    public function changelogs(): Collection
    {
        return Changelog::query()
            ->withCount('items')
            ->orderByDesc('released_at')
            ->get();
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $this->editingId = $id;
        $this->showForm = true;
    }

    public function delete(Changelog $changelog): void
    {
        $this->authorize(Permission::ManageChangelog->value);

        $changelog->delete();

        $this->notification()->success(
            title: __('changelog.admin.saved'),
            description: __('changelog.admin.deleted'),
        );

        unset($this->changelogs);
    }

    public function runSeeder(): void
    {
        $this->authorize(Permission::ManageChangelog->value);

        $seederClass = $this->selectedSeeder;

        if (! array_key_exists($seederClass, self::SEEDERS)) {
            $this->notification()->error(
                title: __('changelog.admin.seeder_invalid'),
            );

            return;
        }

        Artisan::call('db:seed', [
            '--class'          => "Database\\Seeders\\{$seederClass}",
            '--no-interaction' => true,
        ]);

        $this->selectedSeeder = '';

        unset($this->changelogs);

        $this->notification()->success(
            title: __('changelog.admin.seeder_success'),
        );
    }

    public function updatedShowForm(bool $value): void
    {
        if (! $value) {
            $this->editingId = null;
        }
    }

    public function onSaved(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        unset($this->changelogs);

        $this->notification()->success(
            title: __('changelog.admin.saved'),
            description: __('changelog.admin.saved'),
        );
    }

    public function render(): View
    {
        return view('livewire.changelog.index');
    }
}
