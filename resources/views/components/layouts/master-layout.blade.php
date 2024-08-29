<x-html title="Yugumo">
    <x-slot name="head">
        @vite('resources/css/app.css')
        @livewireStyles
        @bukStyles
    </x-slot>

    {{ $slot }}

    @livewireScripts
    @bukScripts
</x-html>
