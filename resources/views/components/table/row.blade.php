@props([
    'interactive' => true,
    'variant' => 'default',
])

<tr {{ $attributes->class([
    'table-row',
    'table-row--interactive' => $interactive,
    'table-row--danger' => $interactive && $variant === 'danger',
]) }}>
    {{ $slot }}
</tr>
