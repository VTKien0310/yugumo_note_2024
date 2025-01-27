<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Features\Note\Actions\ListNoteOfUserAction;
use App\Features\Note\Actions\MakeNoteListDisplayDataAction;
use App\Features\Note\Models\Note;
use App\Features\Note\ValueObjects\NoteListDisplayDataValueObject;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        $paginatedNotes = app()->make(ListNoteOfUserAction::class)->handle();

        $makeNoteListDisplayDataAction = app()->make(MakeNoteListDisplayDataAction::class);
        $notes = array_map(
            fn(Note $note): NoteListDisplayDataValueObject => $makeNoteListDisplayDataAction->handle($note),
            $paginatedNotes->items()
        );

        return [
            'notes' => $notes,
        ];
    }
}; ?>

<div class="overflow-x-auto px-5">
    <table class="table">
        <thead>
        <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Content</th>
            <th>Updated at</th>
            <th>Created at</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @php /* @var NoteListDisplayDataValueObject[] $notes */ @endphp
        @foreach ($notes as $note)
            <tr class="hover">
                <td>{{ $note->title }}</td>
                <td>{{ $note->type }}</td>
                <td>{{ $note->shortenedContent }}</td>
                <td>{{ $note->updatedAt }}</td>
                <td>{{ $note->createdAt }}</td>
                <td>
                    <div class="w-full h-full flex flex-row justify-start items-center">
                        <a href="{{ route('notes.show', ['note' => $note->id]) }}">
                            <button class="btn btn-sm btn-square btn-primary">
                                <x-ionicon-information class="h-3 w-3"/>
                            </button>
                        </a>

                        <div>
                            <button class="btn btn-sm btn-square btn-error ml-2">
                                <x-ionicon-trash class="h-3 w-3"/>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
