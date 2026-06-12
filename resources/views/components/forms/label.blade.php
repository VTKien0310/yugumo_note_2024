@props(['for' => ''])

<label for="{{ $for }}" {{ $attributes }}>
    {{ $slot->isNotEmpty() ? $slot : ucfirst(str_replace('_', ' ', $for)) }}
</label>
