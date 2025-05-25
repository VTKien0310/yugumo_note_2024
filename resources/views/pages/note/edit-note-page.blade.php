@use(App\Features\NoteType\Enums\NoteTypeEnum)

<x-layouts.master-layout>
    <x-slot:pageTitle>{{ $note->title }}</x-slot:pageTitle>

    <x-layouts.authenticated-layout>
        <div class="w-full flex flex-col justify-start items-center">
            <div class="w-3/4 xl:w-1/2 flex justify-between items-center mb-3">
                <p class="text-sm font-bold">{{ $noteType->name }}</p>
                <livewire:bookmark-note-button-livewire :note="$note"/>
            </div>
            @switch($note->type_id)
                @case(NoteTypeEnum::SIMPLE->value)
                    <livewire:edit-simple-note-form-livewire :note="$note"/>
                    @break
                @case(NoteTypeEnum::ADVANCED->value)
                    <livewire:edit-advanced-note-form-livewire :note="$note"/>
                    @break
                @case(NoteTypeEnum::CHECKLIST->value)
                    <livewire:edit-checklist-note-form-livewire :note="$note"/>
                    @break
                @case(NoteTypeEnum::XML->value)
                    <livewire:edit-xml-note-form-livewire :note="$note"/>
                    @break
                @default
                    <p>Work in progress ...</p>
            @endswitch
        </div>
    </x-layouts.authenticated-layout>
</x-layouts.master-layout>
