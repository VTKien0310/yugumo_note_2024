<?php

use App\Features\Note\Actions\UpdateNoteAction;
use Livewire\Volt\Component;
use App\Features\Note\Models\Note;
use Illuminate\Database\Eloquent\Collection;
use App\Features\Note\Actions\FindChecklistContentOfNoteForDisplayAction;
use App\Features\Note\Actions\CreateEmptyChecklistNoteContentAction;
use App\Features\Note\Models\ChecklistNoteContent;

new class extends Component {
    public Note $note;

    public string $title;

    public Collection $content;

    public function mount(Note $note): void
    {
        $this->note = $note;
        $this->title = $note->title;
        $this->refreshChecklistContent();
    }

    private function refreshChecklistContent(): void
    {
        $this->content = app()->make(FindChecklistContentOfNoteForDisplayAction::class)->handle($this->note);
    }

    public function updated(): void
    {
        app()->make(UpdateNoteAction::class)->handle($this->note, [
            'title' => $this->title,
        ]);
    }

    public function addNewChecklistItem(): void
    {
        app()->make(CreateEmptyChecklistNoteContentAction::class)->handle($this->note);
        $this->refreshChecklistContent();
    }

    public function removeChecklistItemFromContent(string $checklistItemId): void
    {
        $this->content = $this->content->reject(
            fn(ChecklistNoteContent $checklistItem): bool => $checklistItem->id === $checklistItemId
        );
    }
}; ?>

<div class="w-3/4 xl:w-1/2">
    <div class="w-full flex flex-col justify-start items-center">

        <div class="w-full flex flex-col justify-start items-start mb-5">
            <x-label for="title" class="font-bold text-xs mb-1"/>
            <x-input wire:model.live.debounce.500ms="title" name="title" class="input input-bordered w-full"/>
        </div>

        <div class="w-full flex flex-col justify-start items-start">
            <div class="w-full flex justify-between items-center content-center mb-1">
                <x-label for="content" class="font-bold text-xs"/>
                <button wire:click="addNewChecklistItem" class="btn-with-centered-icon btn btn-xs btn-primary">
                    <x-ionicon-add class="w-6 h-6"/>
                </button>
            </div>
            <div class="w-full flex flex-col">
                @foreach($content as $checklistItem)
                    <livewire:checklist-item-form-livewire
                            :checklist-item="$checklistItem"
                            :key="$checklistItem->id"
                            @checklist-item-deleted="removeChecklistItemFromContent($event.detail.id)"
                    />
                @endforeach
            </div>
        </div>

    </div>
</div>
