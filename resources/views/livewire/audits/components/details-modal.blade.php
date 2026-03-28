@props(['audit'])

<x-modal-card wire:model="showDetailsModal" title="{{ __('audits.modal.title') }}" max-width="2xl">
    @if($audit)
        <div class="space-y-5">

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('audits.modal.model') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ class_basename($audit->auditable_type) }} <span class="text-gray-400">#{{ $audit->auditable_id }}</span>
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('audits.modal.event') }}</p>
                    <div class="mt-0.5">
                        @include('livewire.audits.components.event-badge', ['event' => $audit->event])
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('audits.modal.user') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $audit->user?->name ?? __('audits.table.system') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('audits.modal.date') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $audit->created_at->format('d/m/Y H:i:s') }}
                    </p>
                </div>
            </div>

            @php
                $fields = collect(array_keys(array_merge($audit->old_values ?? [], $audit->new_values ?? [])));
            @endphp

            @if($fields->isNotEmpty())
                <div class="border border-gray-100 dark:border-gray-700 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase w-1/4">{{ __('audits.modal.field') }}</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase w-[37.5%]">{{ __('audits.modal.old_value') }}</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase w-[37.5%]">{{ __('audits.modal.new_value') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($fields as $field)
                                @php
                                    $oldVal = $audit->old_values[$field] ?? null;
                                    $newVal = $audit->new_values[$field] ?? null;
                                    $changed = $oldVal !== $newVal;
                                @endphp
                                <tr class="{{ $changed ? 'bg-white dark:bg-gray-800' : '' }}">
                                    <td class="px-4 py-2.5 font-mono text-xs text-gray-600 dark:text-gray-400 font-medium">
                                        {{ $field }}
                                    </td>
                                    <td class="px-4 py-2.5">
                                        @if($oldVal !== null)
                                            <span class="inline-block px-2 py-0.5 rounded bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400 font-mono text-xs break-all">
                                                {{ is_array($oldVal) ? json_encode($oldVal) : $oldVal }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        @if($newVal !== null)
                                            <span class="inline-block px-2 py-0.5 rounded bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 font-mono text-xs break-all">
                                                {{ is_array($newVal) ? json_encode($newVal) : $newVal }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                    {{ __('audits.modal.no_changes') }}
                </p>
            @endif
        </div>
    @endif

    <x-slot name="footer">
        <div class="flex justify-end">
            <x-button flat label="{{ __('audits.modal.close') }}" x-on:click="close" />
        </div>
    </x-slot>
</x-modal-card>
