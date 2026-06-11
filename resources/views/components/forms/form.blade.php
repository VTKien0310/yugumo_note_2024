@props([
    'action' => null,
    'method' => 'POST',
    'hasFiles' => false,
])

<form
    method="{{ $method !== 'GET' ? 'POST' : 'GET' }}"
    @isset($action) action="{{ $action }}" @endisset
    {!! $hasFiles ? 'enctype="multipart/form-data"' : '' !!}
    {{ $attributes }}
>
    @csrf
    @method(strtoupper($method))

    {{ $slot }}
</form>
