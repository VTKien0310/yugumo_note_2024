<x-html :title="empty($pageTitle) ? 'Yugumo' : 'Yugumo | '.$pageTitle">
    <x-slot name="head">
        <link rel="icon" href="{{ Vite::asset('resources/images/favicon.ico') }}"/>

        {{-- Load brand name font --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">

        {{-- Trix editor --}}
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@latest/dist/trix.css">
        <script type="text/javascript" src="https://unpkg.com/trix@latest/dist/trix.umd.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @bukStyles
    </x-slot>

    {{ $slot }}

    @livewireScripts
    @bukScripts
</x-html>
