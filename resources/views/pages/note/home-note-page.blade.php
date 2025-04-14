@php
    use App\Features\Note\ValueObjects\NoteListDisplayDataValueObject;
@endphp
<x-layouts.master-layout>
    <x-slot:pageTitle>Home</x-slot:pageTitle>

    <x-layouts.authenticated-layout>
        <div class="flex flex-col justify-center items-center">
            <div class="w-full">
                <h1 class="w-full text-start text-2xl">Recently viewed</h1>
                <div class="w-full grid grid-cols-3 gap-2 mb-5 px-5">
                    @php /** @var NoteListDisplayDataValueObject $note */ @endphp
                    @foreach($recentlyViewedNotes as $note)
                        <div class="card bg-base-100 w-full h-full shadow-xl">
                            <div class="card-body">
                                <div class="badge badge-ghost">{{ $note->type }}</div>
                                <h2 class="card-title">{{ $note->shortTitle }}</h2>
                                <p>{{ $note->shortContent }}</p>
                                <div class="card-actions justify-end">
                                    <a href="{{ route('notes.show', ['note' => $note->id]) }}">
                                        <button class="btn btn-sm btn-square btn-primary">
                                            <x-ionicon-information class="h-4 w-4"/>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-layouts.authenticated-layout>
</x-layouts.master-layout>
