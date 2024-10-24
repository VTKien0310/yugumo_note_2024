<?php

use App\Features\Note\Actions\UpdateNoteAction;
use Livewire\Volt\Component;
use App\Features\Note\Models\Note;
use Illuminate\Database\Eloquent\Collection;
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
                    <livewire:checklist-item-form-livewire :checklist-item="$checklistItem"/>
                @endforeach
            </div>
        </div>
    </x-form>
</div>
