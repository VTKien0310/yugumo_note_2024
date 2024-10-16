<?php

use Livewire\Volt\Component;
use App\Features\Note\Models\Note;
use App\Features\Note\Actions\UpdateNoteByIdAction;

new class extends Component {
    public string $id;

    public string $title;

    public string $content;

    public function mount(Note $note): void
    {
        $this->id = $note->id;
        $this->title = $note->title;
        $this->content = $note->content['content'];
    }

    public function updated(): void
    {
        app()->make(UpdateNoteByIdAction::class)->handle($this->id, [
            'title' => $this->title,
            'content' => [
                'content' => $this->content,
            ],
        ]);
    }
}; ?>

<div class="w-3/4 xl:w-1/2">
    <x-form class="w-full flex flex-col justify-start items-center">
        <div class="w-full flex flex-col justify-start items-start mb-5">
            <x-label for="title" class="font-bold text-xs"/>
            <x-input wire:model.live.debounce.500ms="title" name="title" class="input input-bordered w-full mt-1"/>
        </div>
        <div class="w-full flex flex-col justify-start items-start">
            <x-label for="content" class="font-bold text-xs"/>
            <x-textarea
                wire:model.live.debounce.500ms="content"
                name="content"
                :rows="20"
                class="textarea textarea-bordered w-full mt-1"
            />
        </div>
    </x-form>
</div>
