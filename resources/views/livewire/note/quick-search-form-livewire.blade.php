<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $fullWidth;

    public function mount(bool $fullWidth = false): void
    {
        $this->fullWidth = $fullWidth;
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
    <input type="text" class="grow" placeholder="Search"/>
    <x-ionicon-search class="h-4 w-4"/>
</label>
