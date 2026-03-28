@props(['audits'])

<div class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col min-h-0 w-full max-w-full">
    <div class="overflow-x-auto flex-1 relative w-full max-w-full">
        <table class="w-full divide-y divide-gray-100 dark:divide-gray-700 border-separate border-spacing-0">
            <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0 z-10">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('audits.table.date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('audits.table.event') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('audits.table.model') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('audits.table.user') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('audits.table.changes') }}</th>
                    <th class="px-6 py-3 border-b border-gray-100 dark:border-gray-700"></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($audits as $audit)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <span title="{{ $audit->created_at->format('d/m/Y H:i:s') }}">
                                {{ $audit->created_at->format('d/m/Y H:i') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @include('livewire.audits.components.event-badge', ['event' => $audit->event])
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ class_basename($audit->auditable_type) }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">#{{ $audit->auditable_id }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $audit->user?->name ?? __('audits.table.system') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $changesCount = count($audit->new_values ?? []) ?: count($audit->old_values ?? []) @endphp
                            @if($changesCount > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $changesCount }} {{ trans_choice('audits.table.fields', $changesCount) }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <x-button
                                xs
                                flat
                                icon="eye"
                                label="{{ __('audits.table.view_diff') }}"
                                wire:click="showDetails({{ $audit->id }})"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('audits.table.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($audits->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            {{ $audits->links() }}
        </div>
    @endif
</div>
