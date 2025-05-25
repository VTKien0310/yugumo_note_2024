<?php

use Livewire\Volt\Component;
use App\Features\Note\Models\Note;
use App\Features\Note\Actions\UpdateNoteAction;

new class extends Component {
    public Note $note;

    public string $title;

    public string $content;

    public string $description;

    public function mount(Note $note): void
    {
        $this->note = $note;
        $this->title = $note->title;
        $this->content = $note->xmlContent->content;
        $this->description = $note->textContent->content;
    }

    public function updated(): void
    {
        app()->make(UpdateNoteAction::class)->handle($this->note, [
            'title' => $this->title,
            'text_content' => $this->description,
            'xml_content' => $this->content,
        ]);
    }
}; ?>

<div class="w-3/4 xl:w-1/2">
    <div class="w-full flex flex-col justify-start items-center">

        <div class="w-full flex flex-col justify-start items-start mb-5">
            <x-label for="title" class="font-bold text-xs mb-1"/>
            <x-input wire:model.live.debounce.500ms="title" name="title" class="input input-bordered w-full"/>
        </div>

        <div class="w-full flex flex-col justify-start items-start mb-5">
            <x-label for="description" class="font-bold text-xs mb-1"/>
            <x-input
                wire:model.live.debounce.500ms="description"
                name="description"
                class="input input-bordered w-full"
            />
        </div>

        <div class="w-full flex flex-col justify-start items-start">
            <x-label for="content" class="font-bold text-xs mb-1"/>
            {{-- hidden input to store xml content from Ace editor --}}
            <x-textarea
                wire:model.live.debounce.500ms="content"
                name="content"
                id="content-input"
                class="hidden"
            />
            {{-- load Ace editor using Alpine.js --}}
            <div class="w-full" wire:ignore x-data x-init="
                const editor = ace.edit($refs.e);
                editor.setTheme('ace/theme/monokai');
                editor.session.setMode('ace/mode/xml');
                editor.setValue(@entangle('content').defer, -1);
                editor.session.on('change', () => @this.set('content', ed.getValue()));
            ">
                <div x-ref="e" class="w-full" style="height: 500px"></div>
            </div>
        </div>
    </div>
</div>
