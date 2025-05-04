<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $email = '';

    public string $password = '';

    public function mount(): void
    {
        $this->email = Auth::user()->email;
    }
}
?>

<div class="w-full flex flex-col items-center justify-start">
    <label class="input w-full">
        <input wire:model="email" type="email" placeholder="mail@site.com" required/>
    </label>
    <label class="input w-full mt-2">
        <input wire:model="password" type="password" placeholder="Password" required/>
    </label>
    <div class="w-full mt-3 flex flex-row items-center justify-between">
        <button class="w-1/3 btn">Reset</button>
        <button class="w-1/3 btn btn-primary">Update</button>
    </div>
</div>
