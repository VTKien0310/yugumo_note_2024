<?php

use Livewire\Volt\Component;
use App\Features\Note\Models\Note;

new class extends Component {
    public Note $note;

    public function mount(Note $note): void
    {
        $this->note = $note;
    }
}; ?>

<div>
    <h1>{{ $note->title }}</h1>
</div>
