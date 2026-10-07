<?php

use App\Features\Note\Models\Note;
use Livewire\Volt\Component;

new class extends Component
{
    public Note $note;

    public function mount(Note $note): void
    {
        $this->note = $note->load(Note::RELATION_TEXT_CONTENT);
    }
}; ?>

<div
    class="w-3/4 xl:w-1/2"
    x-data="markdownNoteEditor({
        syncUrl: @js(route('bff.notes.update', ['note' => $this->note->id])),
        initialTitle: @js($this->note->title),
        initialContent: @js($this->note->textContent->content ?? ''),
    })"
>
    <div class="w-full flex flex-col justify-start items-center">
        <div class="w-full flex flex-col justify-start items-start mb-5">
            <x-forms.label for="title" class="font-bold text-xs mb-1"/>
            <x-forms.input
                name="title"
                maxlength="255"
                :value="$this->note->title"
                class="input input-bordered w-full"
                x-ref="alpTitleInput"
                x-on:input="alpUnsaved = alpHasUnsavedChanges()"
            />
        </div>
        <div class="w-full flex flex-col justify-start items-start">
            <x-forms.label for="content" class="font-bold text-xs mb-1"/>
            <div class="w-full block" wire:ignore>
                <textarea id="content" x-ref="alpEditor" class="easymde-editor-host"></textarea>
            </div>
        </div>
        <div class="w-full flex flex-row justify-end items-center gap-3 pt-4">
            <span x-show="alpUnsaved && ! alpSyncing && alpSaveState !== 'failed'" style="display: none;" class="text-xs text-warning">Unsaved changes</span>
            <span x-show="alpSaveState === 'saving'" style="display: none;" class="text-xs text-info">Saving...</span>
            <span x-show="alpSaveState === 'saved' && ! alpUnsaved" style="display: none;" class="text-xs text-success">Saved</span>
            <span x-show="alpSaveState === 'failed' && alpUnsaved" style="display: none;" class="text-xs text-error">Sync failed - will retry automatically</span>
        </div>
    </div>
</div>
