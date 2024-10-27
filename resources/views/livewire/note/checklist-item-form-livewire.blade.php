<?php

use Livewire\Volt\Component;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Actions\UpdateChecklistNoteContentAction;
use App\Extendables\Core\Utils\BoolIntValueEnum;

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
            ChecklistNoteContent::IS_COMPLETED => BoolIntValueEnum::from($this->isCompleted)
        ]);

        $this->extractDataFromChecklistContent($checklistItem);
    }

    private function extractDataFromChecklistContent(ChecklistNoteContent $checklistItem): void
    {
        $this->content = $checklistItem->content;
        $this->isCompleted = $checklistItem->is_completed->value;
    }
}; ?>

<label class="label cursor-pointer">
    <input
        type="text"
        wire:model.live.debounce.500ms="content"
        value="{{ $content }}"
        @class(['text-decoration-line: line-through'=> $isCompleted])
        class="input input-ghost"
        style="width: 100%;" {{-- workaround for style overriding from packages and libraries --}}
    />
    <div class="flex flex-row justify-end items-center content-center">
        <input
            type="checkbox"
            wire:model.live.debounce.500ms="isCompleted"
            @checked($isCompleted)
            class="checkbox checkbox-primary ml-1"
        />
        <button class="btn-with-centered-icon btn btn-error btn-xs btn-square btn-outline ml-1">
            <x-ionicon-close class="h-6 w-6"/>
        </button>
    </div>
</label>
