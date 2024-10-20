<?php

use Livewire\Volt\Component;
use App\Features\Note\Models\Note;
use Illuminate\Database\Eloquent\Collection;

new class extends Component {
    public string $id;

    public string $title;

    public Collection $content;

    public function mount(Note $note): void
    {
        $this->id = $note->id;
        $this->title = $note->title;
        $this->content = $note->checklistContent;
    }

    public function updated(): void
    {
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
                                class="checkbox checkbox-primary"
                            />
                            <button class="btn btn-error btn-xs btn-square btn-outline ml-1">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    </x-form>
</div>
