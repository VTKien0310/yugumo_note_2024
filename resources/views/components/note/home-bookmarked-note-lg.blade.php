@use(App\Features\Note\ValueObjects\NoteListDisplayDataValueObject)

@props(['note'])

@php /** @var NoteListDisplayDataValueObject $note */ @endphp
<li class="list-row">
    <button class="btn btn-ghost w-16 h-full">
        <x-ionicon-bookmark class="w-8 h-8"/>
    </button>
    <div class="list-col-grow">
        <div class="badge badge-ghost mb-2">{{ $note->type }}</div>
        <p class="font-semibold">{{ $note->mediumTitle }}</p>
        <p>{{ $note->mediumContent }}</p>
    </div>
    <a href="{{ route('notes.show', ['note' => $note->id]) }}">
        <button class="btn btn-circle">
            <x-ionicon-arrow-forward class="w-4 h-4"/>
        </button>
    </a>
</li>
