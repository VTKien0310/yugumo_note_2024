<x-layouts.master-layout>
    <x-slot:pageTitle>{{ $note->title }}</x-slot:pageTitle>

    <x-layouts.authenticated-layout>
        <livewire:edit-note-form-livewire :note="$note"/>
    </x-layouts.authenticated-layout>
</x-layouts.master-layout>
