@props(['event'])

@php
    $config = match($event) {
        'created'  => ['bg' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400', 'icon' => 'plus-circle',       'label' => __('audits.events.created')],
        'updated'  => ['bg' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',    'icon' => 'pencil-square',      'label' => __('audits.events.updated')],
        'deleted'  => ['bg' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',        'icon' => 'trash',              'label' => __('audits.events.deleted')],
        'restored' => ['bg' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400', 'icon' => 'arrow-uturn-left', 'label' => __('audits.events.restored')],
        default    => ['bg' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',        'icon' => 'bolt',               'label' => $event],
    };
@endphp

<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium {{ $config['bg'] }}">
    <x-icon name="{{ $config['icon'] }}" class="w-3.5 h-3.5" />
    {{ $config['label'] }}
</span>
