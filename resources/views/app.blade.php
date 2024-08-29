<x-html title="Yugumo">
    <x-slot name="head">
        @vite('resources/css/app.css')
        @livewireStyles
        @bukStyles
    </x-slot>

    <h1 class="text-3xl font-bold underline">
        Hello world!
    </h1>

    @livewireScripts
    @bukScripts
</x-html>
