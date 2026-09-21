<?php

use App\Features\Note\Actions\UpdateNoteAction;
use App\Features\Note\Models\Note;
use Livewire\Volt\Component;

new class extends Component
{
    public Note $note;

    public string $title;

    public string $content;

    public function mount(Note $note): void
    {
        $this->note = $note;
        $this->title = $note->title;
        $this->content = json_encode($note->richTextContent->content);
    }

    public function updated(): void
    {
        app()->make(UpdateNoteAction::class)->handle($this->note, [
            'title' => $this->title,
            'rich_text_content' => $this->content,
        ]);
    }
}; ?>

<div class="w-3/4 xl:w-1/2">
    <div class="w-full flex flex-col justify-start items-center">
        <div class="w-full flex flex-col justify-start items-start mb-5">
            <x-forms.label for="title" class="font-bold text-xs mb-1"/>
            <x-forms.input
                wire:model.live.debounce.500ms="title"
                name="title"
                class="input input-bordered w-full"
            />
        </div>
        <div class="w-full flex flex-col justify-start items-start">
            <x-forms.label for="content" class="font-bold text-xs mb-1"/>
            <x-forms.quill
                wire:model.live.debounce.500ms="content"
                name="content"
                class="w-full block"
            />
        </div>
    </div>
</div>
