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
                <button class="btn btn-sm btn-square btn-primary">
                    <x-ionicon-information class="h-4 w-4"/>
                </button>
            </a>
        </div>
    </div>
</div>
