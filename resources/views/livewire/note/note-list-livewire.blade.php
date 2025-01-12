<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Features\Note\Actions\ListNoteOfUserAction;


new class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            'notes' => app()->make(ListNoteOfUserAction::class)->handle(),
        ];
    }
}; ?>

<div>
    <div>
        <div>
            @foreach ($notes as $note)
                <p>{{ $note->id }}</p>
            @endforeach
        </div>

        {{ $notes->links() }}
    </div>
</div>

