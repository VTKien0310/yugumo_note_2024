<?php

use App\Features\Note\Actions\UpdateNoteAction;
use Livewire\Volt\Component;
use App\Features\Note\Models\Note;
use Illuminate\Database\Eloquent\Collection;
use App\Features\Note\Actions\UpdateChecklistNoteContentByIdAction;
use App\Features\Note\Actions\FindChecklistContentOfNoteForDisplayAction;

new class extends Component {
    public Note $note;

    public string $title;

    public Collection $content;

    public function mount(Note $note): void
    {
        $this->note = $note;
        $this->title = $note->title;
        $this->content = $this->loadChecklistContent($this->note);
    }

    private function loadChecklistContent(Note $note): Collection
    {
        return app()->make(FindChecklistContentOfNoteForDisplayAction::class)->handle($note);
    }

    public function updated(): void
    {
        app()->make(UpdateNoteAction::class)->handle($this->note, [
            'title' => $this->title,
        ]);
    }

    public function updateChecklistItemContent(string $id, string $content, int $isCompleted): void
    {
        app()->make(UpdateChecklistNoteContentByIdAction::class)->handle($id, [
            'content' => $content,
            'is_completed' => $isCompleted
        ]);
        $this->content = $this->loadChecklistContent($this->note);
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
            <div class="w-full flex flex-col">
                @foreach($content as $checklistItem)
                    <label data-checklist-item-id="{{ $checklistItem->id }}" class="label cursor-pointer">
                        <input
                            type="text"
                            value="{{ $checklistItem->content }}"
                            @class(['text-decoration-line: line-through'=> $checklistItem->is_completed])
                            class="input input-ghost"
                            style="width: 100%;" {{-- workaround for style overriding from packages and libraries --}}
                        />
                        <div class="flex flex-row justify-end items-center content-center">
                            <input
                                type="checkbox"
                                @checked($checklistItem->is_completed)
                                class="checkbox checkbox-primary ml-1"
                            />
                            <button class="delete-checklist-item-btn btn btn-error btn-xs btn-square btn-outline ml-1">
                                <x-ionicon-close class="h-6 w-6"/>
                            </button>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    </x-form>
</div>
