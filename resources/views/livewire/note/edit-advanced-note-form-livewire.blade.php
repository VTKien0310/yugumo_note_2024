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

    public function save(): void
    {
        app()->make(UpdateNoteAction::class)->handle($this->note, [
            'title' => $this->title,
            'rich_text_content' => $this->content,
        ]);

        $this->dispatch('note-saved');
    }
}; ?>

<div class="w-3/4 xl:w-1/2">
    <div class="w-full flex flex-col justify-start items-center">
        <div class="w-full flex flex-col justify-start items-start mb-5">
            <x-forms.label for="title" class="font-bold text-xs mb-1"/>
            <x-forms.input
                wire:model="title"
                name="title"
                class="input input-bordered w-full"
            />
        </div>
        <div class="w-full flex flex-col justify-start items-start">
            <x-forms.label for="content" class="font-bold text-xs mb-1"/>
            <x-forms.quill
                wire:model="content"
                name="content"
                class="w-full block"
            />
        </div>
        <div
            class="w-full flex flex-row justify-end items-center gap-3 pt-4"
            x-data="{
                alpDirty: false,
                alpInit() {
                    this.$wire.$watch('title', () => { this.alpDirty = true; });
                    this.$wire.$watch('content', () => { this.alpDirty = true; });

                    window.addEventListener('beforeunload', (e) => {
                        if (! this.alpDirty) return;
                        e.preventDefault();
                        e.returnValue = '';
                    });
                },
            }"
            x-init="alpInit()"
            @note-saved.window="alpDirty = false"
        >
            <span x-show="alpDirty" style="display: none;" class="text-xs text-warning">Unsaved changes</span>
            <button
                type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
                class="btn btn-primary"
            >
                <span wire:loading.remove wire:target="save">Save</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </div>
</div>
