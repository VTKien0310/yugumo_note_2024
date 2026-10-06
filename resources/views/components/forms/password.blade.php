@props([
    'name' => 'password',
    'id' => null,
])

@php
    $resolvedId = $id ?? $name;
@endphp

<input
    name="{{ $name }}"
    type="password"
    id="{{ $resolvedId }}"
    {{ $attributes }}
/>
