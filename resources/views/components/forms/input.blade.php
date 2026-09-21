@props([
    'name' => '',
    'id' => null,
    'type' => 'text',
    'value' => '',
])

@php
    $resolvedId = $id ?? $name;
    $resolvedValue = old($name, $value);
@endphp

<input
    name="{{ $name }}"
    type="{{ $type }}"
    id="{{ $resolvedId }}"
    @if($resolvedValue) value="{{ $resolvedValue }}" @endif
    {{ $attributes }}
/>
