@php
    use App\Features\Note\ValueObjects\NoteListDisplayDataValueObject;
@endphp

@props(['note'])

@php /** @var NoteListDisplayDataValueObject $note */ @endphp
<div class="card bg-base-100 w-full h-full shadow-xl">
    <div class="card-body">
        <div class="badge badge-ghost">{{ $note->type }}</div>
        <h2 class="card-title">{{ $note->mediumTitle }}</h2>
        <p>{{ $note->mediumContent }}</p>
        <div class="card-actions justify-end">
            <a href="{{ route('notes.show', ['note' => $note->id]) }}">
                <button class="btn btn-sm btn-circle">
                    <x-ionicon-arrow-forward class="w-4 h-4"/>
                </button>
            </a>
        </div>
    </div>
</div>
