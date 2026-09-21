<?php

use App\Features\NoteType\Actions\MakeAllNoteTypeViewDataAction;
use App\Features\NoteType\ValueObjects\NoteTypeViewDataValueObject;
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
}; ?>

<div class="w-full flex flex-wrap flex-row justify-center content-center items-center gap-5 px-5 pb-8">
    @foreach($noteTypes as $noteType)
        <div class="card bg-base-100 w-96 shadow-xl">
            <figure>
                <img src="{{ $noteType->illustrationPath }}" alt="{{ $noteType->illustrationAlt }}"/>
            </figure>
            <div class="card-body">
                <h2 class="card-title">{{ $noteType->name }}</h2>
                <p class="mt-1">{{ $noteType->description }}</p>
                <div class="card-actions justify-center">
                    <form action="{{ route('note-types.notes.store', ['noteType' => $noteType->id]) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block">
                            Add
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
