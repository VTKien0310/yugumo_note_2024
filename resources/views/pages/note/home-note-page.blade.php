@php
    use App\Features\Note\ValueObjects\NoteListDisplayDataValueObject;
@endphp
<x-layouts.master-layout>
    <x-slot:pageTitle>Home</x-slot:pageTitle>

    <x-layouts.authenticated-layout>
        <div class="flex flex-col justify-center items-center">
            <div class="w-full mt-5">
                <h1 class="w-full text-start text-2xl pl-5">Recently viewed</h1>
                <div class="w-full grid grid-cols-3 gap-2 mb-5 px-5">
                    @php /** @var NoteListDisplayDataValueObject $note */ @endphp
                    @foreach($recentlyViewedNotes as $note)
                        <x-note.home-note-card :note="$note"/>
                    @endforeach
                </div>
            </div>
            <div class="divider px-5"></div>
            <div class="w-full mt-5">
                <h1 class="w-full text-start text-2xl pl-5">Frequently viewed</h1>
                <div class="w-full grid grid-cols-3 gap-2 mb-5 px-5">
                    @php /** @var NoteListDisplayDataValueObject $note */ @endphp
                    @foreach($trendingNotes as $note)
                        <x-note.home-note-card :note="$note"/>
                    @endforeach
                </div>
            </div>
        </div>
    </x-layouts.authenticated-layout>
</x-layouts.master-layout>
