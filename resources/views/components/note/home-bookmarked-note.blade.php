@use(App\Features\Note\ValueObjects\NoteListDisplayDataValueObject)

@props(['note'])

@php /** @var NoteListDisplayDataValueObject $note */ @endphp
<li class="list-row">
    <div class="w-32 badge badge-ghost mb-2">{{ $note->type }}</div>
    <div class="list-col-grow">
        <p class="font-semibold">{{ $note->mediumTitle }}</p>
        <p>{{ $note->mediumContent }}</p>
    </div>
    <a href="{{ route('notes.show', ['note' => $note->id]) }}">
        <button class="btn btn-circle">
            <x-ionicon-arrow-forward class="w-4 h-4"/>
        </button>
    </a>
</li>
