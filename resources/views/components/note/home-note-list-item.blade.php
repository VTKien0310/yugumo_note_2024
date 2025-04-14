@php
    use App\Features\Note\ValueObjects\NoteListDisplayDataValueObject;
@endphp

@props(['note'])

@php /** @var NoteListDisplayDataValueObject $note */ @endphp
<a href="{{ route('notes.show', ['note' => $note->id]) }}" class="list-row">
    <div>
        <div>{{ $note->shortTitle }}</div>
        <div class="text-xs uppercase font-semibold opacity-60">{{ $note->type }}</div>
    </div>
    <p class="list-col-wrap text-xs">{{ $note->shortContent }}</p>
    <div class="divider"></div>
</a>
