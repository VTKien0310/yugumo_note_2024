@use(App\Features\Note\ValueObjects\NoteListDisplayDataValueObject)

@props(['note'])

@php /** @var NoteListDisplayDataValueObject $note */ @endphp
<li class="list-row">
    <div class="list-col-grow">
        <p class="badge badge-ghost mb-2">{{ $note->type }}</p>
        <p class="font-semibold">{{ $note->shortTitle }}</p>
    </div>
    <p class="list-col-wrap">{{ $note->shortContent }}</p>
    <a href="{{ route('notes.show', ['note' => $note->id]) }}">
        <button class="btn btn-circle">
            <x-ionicon-arrow-forward class="w-4 h-4"/>
        </button>
    </a>
</li>
