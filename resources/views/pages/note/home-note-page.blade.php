@use(App\Features\Note\ValueObjects\NoteListDisplayDataValueObject)
@use(App\Features\Note\Models\Note)
<x-layouts.master-layout>
    <x-slot:pageTitle>Home</x-slot:pageTitle>

    <x-layouts.authenticated-layout>

        @if(session()->has('remove-bookmark-success'))
            <div x-data="{ display: true }" class="toast toast-top toast-center">
                <div x-show="display" class="alert alert-success">
                    <p>{{ session('remove-bookmark-success') }}</p>
                    <button @click="display = false" class="btn btn-circle btn-ghost btn-xs">
                        <x-ionicon-close class="h-4 w-4"/>
                    </button>
                </div>
            </div>
        @endif

        <div class="flex flex-col justify-center items-center">

            <div class="w-full mt-5">
                <h1 class="w-full font-bold text-start text-2xl pl-5">
                    Bookmarked ({{ count($bookmarkedNotes) }}/{{ Note::maxBookmarkedNotesPerUser() }})
                </h1>
                <div class="w-full hidden md:block lg:block xl:block mb-5 px-5">
                    <ul class="w-full list">
                        @php /** @var NoteListDisplayDataValueObject $note */ @endphp
                        @foreach($bookmarkedNotes as $note)
                            <x-note.home-bookmarked-note-lg :note="$note"/>
                        @endforeach
                    </ul>
                </div>
                <div class="w-full md:hidden lg:hidden xl:hidden mb-5 mt-5 px-5">
                    <ul class="w-full list">
                        @php /** @var NoteListDisplayDataValueObject $note */ @endphp
                        @foreach($bookmarkedNotes as $note)
                            <x-note.home-bookmarked-note-sm :note="$note"/>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="hidden md:block lg:block xl:block divider px-5"></div>

            <div class="w-full mt-5">
                <h1 class="w-full font-bold text-start text-2xl pl-5">Recently viewed</h1>
                <div class="w-full hidden md:grid lg:grid xl:grid grid-cols-3 xl:grid-cols-6 gap-2 mb-5 px-5">
                    @php /** @var NoteListDisplayDataValueObject $note */ @endphp
                    @foreach($recentlyViewedNotes as $note)
                        <x-note.home-note-card :note="$note"/>
                    @endforeach
                </div>
                <div class="w-full md:hidden lg:hidden xl:hidden mb-5 mt-5 px-5">
                    <ul class="w-full list">
                        @php /** @var NoteListDisplayDataValueObject $note */ @endphp
                        @foreach($recentlyViewedNotes as $note)
                            <x-note.home-note-list-item :note="$note"/>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="hidden md:block lg:block xl:block divider px-5"></div>

            <div class="w-full mt-5">
                <h1 class="w-full font-bold text-start text-2xl pl-5">Frequently viewed</h1>
                <div class="w-full hidden md:grid lg:grid xl:grid grid-cols-3 xl:grid-cols-6 gap-2 mb-5 px-5">
                    @php /** @var NoteListDisplayDataValueObject $note */ @endphp
                    @foreach($trendingNotes as $note)
                        <x-note.home-note-card :note="$note"/>
                    @endforeach
                </div>
                <div class="w-full md:hidden lg:hidden xl:hidden mb-5 mt-5 px-5">
                    <ul class="w-full list">
                        @php /** @var NoteListDisplayDataValueObject $note */ @endphp
                        @foreach($trendingNotes as $note)
                            <x-note.home-note-list-item :note="$note"/>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </x-layouts.authenticated-layout>
</x-layouts.master-layout>
