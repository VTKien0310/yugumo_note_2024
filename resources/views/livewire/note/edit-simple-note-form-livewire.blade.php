<?php

use Livewire\Volt\Component;
use App\Features\Note\Models\Note;

new class extends Component {
    public string $title;

    public string $content;

    public function mount(Note $note): void
    {
        $this->title = $note->title;
        $this->content = $note->content;
    }
}; ?>

<div class="w-1/2">
    <x-form class="w-full flex flex-col justify-start items-center">
        <div class="w-full flex flex-col justify-start items-start mb-5">
            <x-label for="title" class="font-bold text-xs"/>
            <x-input wire:model="title" name="title" class="input input-bordered w-full mt-1"/>
        </div>
        <div class="w-full flex flex-col justify-start items-start">
            <x-label for="content" class="font-bold text-xs"/>
            <x-textarea wire:model="content" name="content" :rows="20" class="textarea textarea-bordered w-full mt-1"/>
        </div>
    </x-form>
</div>
