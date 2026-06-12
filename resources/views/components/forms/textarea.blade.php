@props([
    'name' => '',
    'id' => null,
    'rows' => 3,
])

@php
    $resolvedId = $id ?? $name;
@endphp

<textarea
    name="{{ $name }}"
    id="{{ $resolvedId }}"
    rows="{{ $rows }}"
    {{ $attributes }}
>{{ old($name, $slot) }}</textarea>
