<?php

use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Actions\DeleteChecklistNoteContentAction;
use App\Features\Note\Actions\UpdateChecklistNoteContentAction;
use App\Features\Note\Models\ChecklistNoteContent;
use Livewire\Volt\Component;

new class extends Component {
    public ChecklistNoteContent $checklistItem;

    public string $content;

    public int $isCompleted;

    public function mount(ChecklistNoteContent $checklistItem): void
    {
        $this->checklistItem = $checklistItem;
        $this->extractDataFromChecklistContent($checklistItem);
    }

    public function updated(): void
    {
        $checklistItem = app()->make(UpdateChecklistNoteContentAction::class)->handle($this->checklistItem, [
            ChecklistNoteContent::CONTENT => $this->content,
            ChecklistNoteContent::IS_COMPLETED => BoolIntValueEnum::from($this->isCompleted),
        ]);

        $this->extractDataFromChecklistContent($checklistItem);
    }

    private function extractDataFromChecklistContent(ChecklistNoteContent $checklistItem): void
    {
        $this->content = $checklistItem->content;
        $this->isCompleted = $checklistItem->is_completed->value;
    }

    public function deleteChecklistItem(): void
    {
        $deleteSuccessfully = app()->make(DeleteChecklistNoteContentAction::class)->handle($this->checklistItem);

        if ($deleteSuccessfully) {
            $this->dispatch('checklist-item-deleted', id: $this->checklistItem->id);
        }
    }
}; ?>

<div
    class="p-0 mb-3 label cursor-pointer"
>
    <input
        type="text"
        id="checklist-item-{{ $checklistItem->id }}-content"
        name="checklist-item-{{ $checklistItem->id }}-content"
        wire:model.live.debounce.500ms="content"
        value="{{ $content }}"
        @class(['w-full', 'p-0', 'input', 'input-ghost', 'line-through' => $isCompleted])
        aria-label="Checklist item content"
    />
    <div class="flex flex-row justify-end items-center content-center">
        <input
            type="checkbox"
            id="checklist-item-{{ $checklistItem->id }}-is-completed"
            name="checklist-item-{{ $checklistItem->id }}-is-completed"
            wire:model.live.debounce.500ms="isCompleted"
            @checked($isCompleted)
            @change="document.getElementById('checklist-item-{{ $checklistItem->id }}-content').classList.toggle('line-through')"
            class="checkbox checkbox-primary ml-1"
            aria-label="Mark as completed"
        />
        <button
            wire:click="deleteChecklistItem"
            class="btn-with-centered-icon btn btn-error btn-xs btn-square btn-outline ml-1"
        >
            <x-ionicon-close class="h-6 w-6"/>
        </button>
    </div>
</div>
