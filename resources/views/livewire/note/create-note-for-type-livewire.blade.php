<?php

use App\Features\Note\Actions\CreateNewNoteWithDefaultContentAction;
use App\Features\NoteType\Actions\FindNoteTypeByIdAction;
use App\Features\NoteType\Actions\MakeAllNoteTypeViewDataAction;
use App\Features\NoteType\Models\NoteType;
use App\Features\NoteType\ValueObjects\NoteTypeViewDataValueObject;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * @var NoteTypeViewDataValueObject[]
     */
    private array $noteTypes;

    public function mount(): void
    {
        $this->noteTypes = app()->make(MakeAllNoteTypeViewDataAction::class)->handle();
    }

    public function with(): array
    {
        return [
            'noteTypes' => $this->noteTypes
        ];
    }

    public function addNoteForType(int $noteTypeId): void
    {
        $noteType = app()->make(FindNoteTypeByIdAction::class)->handle($noteTypeId);

        $noteType ? $this->noteTypeFound($noteType) : $this->noteTypeNotFound();
    }

    private function noteTypeFound(NoteType $noteType): void
    {
        $newlyCreatedNote = app()->make(CreateNewNoteWithDefaultContentAction::class)->handle(Auth::user(), $noteType);

        $this->redirectRoute('notes.show', [
            'note' => $newlyCreatedNote->id
        ]);
    }

    private function noteTypeNotFound(): void
    {
        session()->flash('note-type-not-found', 'The selected note type is not available.');

        $this->redirectRoute('notes.create');
    }
}; ?>

<div class="w-full flex flex-row justify-center content-center items-center gap-5 px-5">
    @foreach($noteTypes as $noteType)
        <div class="card bg-base-100 w-96 shadow-xl">
            <figure>
                <img src="{{ $noteType->illustrationPath }}" alt="{{ $noteType->illustrationAlt }}"/>
            </figure>
            <div class="card-body">
                <h2 class="card-title">{{ $noteType->name }}</h2>
                <p class="mt-1">{{ $noteType->description }}</p>
                <div class="card-actions justify-center">
                    <button wire:click="addNoteForType({{ $noteType->id }})" class="btn btn-primary btn-block">
                        Add
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>
