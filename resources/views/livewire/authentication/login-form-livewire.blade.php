<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $email = '';

    public string $password = '';

    public function loginAttempt(): void
    {
        $credentials = $this->validatedData();

        $authenticateSuccess = Auth::attempt($credentials, true);

        $authenticateSuccess ? $this->onAuthAttemptSuccess() : $this->onAuthAttemptFail();
    }

    private function validatedData(): array
    {
        return $this->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
    }

    private function onAuthAttemptSuccess(): void
    {
        session()->regenerate();
        $this->redirectIntended();
    }

    private function onAuthAttemptFail(): void
    {
        session()->flash('login-failed', 'Invalid email or password.');
    }
}
?>

<div class="card w-full lg:w-1/2 xl:w-1/4 h-1/2 min-h-fit flex flex-col justify-center items-center bg-base-100 rounded-lg shadow-xl">
    <h1 class="mb-16 text-4xl brand-name-font">YUGUMO</h1>

    <x-form wire:submit="loginAttempt" id="login-form" class="flex flex-col justify-around items-center mb-8">
        <div class="w-full flex flex-col justify-center items-center mb-4">
            <div class="w-full text-left flex flex-row justify-between items-center mb-1">
                <x-label for="email" class="mr-6"/>
                <x-input wire:model="email" name="email" class="input input-bordered"/>
            </div>
            @error('email')
            <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="w-full flex flex-col justify-center items-center">
            <div class="w-full text-left flex flex-row justify-between items-center mb-1">
                <x-label for="password" class="mr-6"/>
                <x-input wire:model="password" name="password" type="password" class="input input-bordered"/>
            </div>
            @error('password')
            <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>
    </x-form>

    <div class="w-2/3 flex flex-col justify-center items-center">
        <button type="submit" form="login-form" class="w-full btn btn-primary mb-2">Log in</button>
        @if(session()->has('login-failed'))
            <div class="w-full alert alert-error">{{ session('login-failed') }}</div>
        @endif
    </div>
</div>
