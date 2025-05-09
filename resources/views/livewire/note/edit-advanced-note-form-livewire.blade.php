<?php

use Livewire\Volt\Component;
use App\Features\Note\Models\Note;
use App\Features\Note\Actions\UpdateNoteAction;

new class extends Component {
    public Note $note;

    public string $title;

    public string $content;

    public function mount(Note $note): void
    {
        $this->note = $note;
        $this->title = $note->title;
        $this->content = $note->textContent->content;
    }

    public function updated(): void
    {
        app()->make(UpdateNoteAction::class)->handle($this->note, [
            'title' => $this->title,
            'text_content' => $this->content,
        ]);
    }
}; ?>

<div class="w-3/4 xl:w-1/2">
    <div class="w-full flex flex-col justify-start items-center">
        <div class="w-full flex flex-col justify-start items-start mb-5">
            <x-label for="title" class="font-bold text-xs mb-1"/>
            <x-input
                wire:model.live.debounce.500ms="title"
                name="title"
                class="input input-bordered w-full"
            />
        </div>
        <div class="w-full flex flex-col justify-start items-start">
            <x-label for="content" class="font-bold text-xs mb-1"/>
            <x-trix
                wire:model.live.debounce.500ms="content"
                name="content"
                class="w-full block"
            />
        </div>
    </div>
</div>
