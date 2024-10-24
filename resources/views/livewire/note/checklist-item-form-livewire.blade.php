<?php

use Livewire\Volt\Component;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Actions\UpdateChecklistNoteContentAction;

new class extends Component {
    public ChecklistNoteContent $checklistItem;
    public string $content;
    public int $is_completed;

    public function mount(ChecklistNoteContent $checklistItem): void
    {
        $this->checklistItem = $checklistItem;
        $this->content = $checklistItem->content;
        $this->is_completed = $checklistItem->is_completed->value;
    }

    public function updated(): void
    {
        $checklistItem = app()->make(UpdateChecklistNoteContentAction::class)->handle($this->checklistItem, [
            ChecklistNoteContent::CONTENT => $this->content
        ]);

        $this->content = $checklistItem->content;
    }
}; ?>

<label data-checklist-item-id="{{ $checklistItem->id }}" class="label cursor-pointer">
    <input
        type="text"
        value="{{ $content }}"
        @class(['text-decoration-line: line-through'=> $is_completed])
        class="input input-ghost"
        style="width: 100%;" {{-- workaround for style overriding from packages and libraries --}}
    />
    <div class="flex flex-row justify-end items-center content-center">
        <input
            type="checkbox"
            @checked($is_completed)
            class="checkbox checkbox-primary ml-1"
        />
        <button class="delete-checklist-item-btn btn btn-error btn-xs btn-square btn-outline ml-1">
            <x-ionicon-close class="h-6 w-6"/>
        </button>
    </div>
</label>
