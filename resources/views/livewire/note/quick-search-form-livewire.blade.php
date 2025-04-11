<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $fullWidth;

    public string $keyword = '';

    public function mount(bool $fullWidth = false): void
    {
        $this->fullWidth = $fullWidth;
    }

    public function search(): void
    {

    }
}; ?>

<label @class([
    'input',
    'input-bordered',
    'flex',
    'items-center',
    'gap-2',
    'ml-2',
    'w-full' => $fullWidth,
])>
    <input wire:model="keyword" @keyup.enter="alert('Submitted!')" type="text" class="grow" placeholder="Search"/>
    <x-ionicon-search class="h-4 w-4"/>
</label>
