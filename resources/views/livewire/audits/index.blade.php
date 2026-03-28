<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    @include('livewire.audits.components.filters', [
        'event'     => $event,
        'auditable' => $auditable,
        'userId'    => $userId,
        'users'     => $this->users,
    ])

    @include('livewire.audits.components.table', ['audits' => $this->audits])

    @include('livewire.audits.components.details-modal', ['audit' => $this->selectedAudit])
</div>
