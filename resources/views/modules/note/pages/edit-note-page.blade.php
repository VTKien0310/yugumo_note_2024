<x-layouts.master-layout>
    <x-slot:pageTitle>{{ $note->title }}</x-slot:pageTitle>

    <x-layouts.authenticated-layout>
        @switch($note->type_id)
            @case(1)
                <livewire:edit-simple-note-form-livewire :note="$note"/>
                @break
            @default
                <p>Work in progress ...</p>
        @endswitch
    </x-layouts.authenticated-layout>
</x-layouts.master-layout>
