@use(App\Features\NoteType\Enums\NoteTypeIdEnum)

<x-layouts.master-layout>
    <x-slot:pageTitle>{{ $note->title }}</x-slot:pageTitle>

    <x-layouts.authenticated-layout>
        <div class="w-full flex flex-col justify-start content-center items-center">
            <p class="mb-3 text-center text-sm font-bold">{{ $noteType->name }}</p>
            @switch($note->type_id)
                @case(NoteTypeIdEnum::SIMPLE->value)
                    <livewire:edit-simple-note-form-livewire :note="$note"/>
                    @break
                @case(NoteTypeIdEnum::ADVANCED->value)
                    <livewire:edit-advanced-note-form-livewire :note="$note"/>
                    @break
                @default
                    <p>Work in progress ...</p>
            @endswitch
        </div>
    </x-layouts.authenticated-layout>
</x-layouts.master-layout>
