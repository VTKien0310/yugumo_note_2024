<?php

use App\Features\Note\Actions\FindChecklistContentOfNoteForDisplayAction;
use App\Features\Note\Models\Note;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

// Render-only shell: all editing (title, item content/completion, item
// create/delete) goes through the BFF endpoints via the checklistNoteEditor
// Alpine component - no Livewire round-trips after the initial render.
new class extends Component
{
    public Note $note;

    public Collection $content;

    public function mount(Note $note): void
    {
        $this->note = $note;
        $this->content = app()->make(FindChecklistContentOfNoteForDisplayAction::class)->handle($note);
    }
}; ?>

<div
    class="w-3/4 xl:w-1/2"
    x-data="checklistNoteEditor({
        titleSyncUrl: @js(route('bff.notes.update', ['note' => $note->id])),
        storeUrl: @js(route('bff.notes.checklist-items.store', ['note' => $note->id])),
        initialTitle: @js($note->title),
    })"
>
    <div class="w-full flex flex-col justify-start items-center">

        <div class="w-full flex flex-col justify-start items-start mb-5">
            <x-forms.label for="title" class="font-bold text-xs mb-1"/>
            <x-forms.input
                name="title"
                maxlength="255"
                :value="$note->title"
                class="input input-bordered w-full"
                x-model="alpTitle"
                x-on:input="alpHandleTitleInput()"
            />
            <div class="w-full flex flex-row justify-end items-center gap-3 pt-1">
                <span x-show="alpTitleSaveState === 'saving'" class="text-xs text-base-content/60">Saving...</span>
                <span x-show="alpTitleSaveState === 'saved' && ! alpTitleUnsaved" class="text-xs text-base-content/60">Saved</span>
                <span x-show="alpTitleSaveState === 'failed'" class="text-xs text-error">Sync failed - will retry on next change</span>
            </div>
        </div>

        <div class="w-full flex flex-col justify-start items-start">
            <div class="w-full flex justify-between items-center content-center mb-1">
                <x-forms.label for="content" class="font-bold text-xs"/>
                <button
                    x-on:click="alpAddItem()"
                    x-bind:disabled="alpAdding"
                    class="btn-with-centered-icon btn btn-xs btn-primary"
                >
                    <x-ionicon-add class="w-6 h-6"/>
                </button>
            </div>
            <div class="w-full flex flex-row justify-end items-center pt-1">
                <span x-show="alpAddFailed" class="text-xs text-error">Could not add item - click + to retry</span>
            </div>
            <div class="w-full flex flex-col pt-2">

                {{-- Items created during this session (newest first, on top of
                     the incomplete section, matching server-side ordering). --}}
                <template x-for="alpItem in alpAddedItems" :key="alpItem.id">
                    <div
                        x-data="checklistNoteEditorItem(alpItem)"
                        x-show="! alpDeleted"
                        x-transition
                        class="p-0 mb-3 label cursor-pointer"
                    >
                        @include('livewire.note.partials.checklist-item-row')
                    </div>
                </template>

                {{-- Server-rendered items. --}}
                @foreach($content as $checklistItem)
                    <div
                        x-data="checklistNoteEditorItem({
                            id: @js($checklistItem->id),
                            content: @js($checklistItem->content),
                            isCompleted: @js((bool) $checklistItem->is_completed->value),
                            updateUrl: @js(route('bff.notes.checklist-items.update', ['note' => $note->id, 'checklistItem' => $checklistItem->id])),
                            deleteUrl: @js(route('bff.notes.checklist-items.destroy', ['note' => $note->id, 'checklistItem' => $checklistItem->id])),
                            position: {{ $loop->index }},
                        })"
                        x-show="! alpDeleted"
                        x-transition
                        class="p-0 mb-3 label cursor-pointer"
                        wire:key="checklist-item-{{ $checklistItem->id }}"
                    >
                        @include('livewire.note.partials.checklist-item-row')
                    </div>
                @endforeach

            </div>
        </div>

    </div>
</div>
