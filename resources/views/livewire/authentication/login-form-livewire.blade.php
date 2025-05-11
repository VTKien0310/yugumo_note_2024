<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $email = '';

    public string $password = '';

    public bool $showFailedToast = false;

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
        $this->showFailedToast = true;
    }
}
?>

<div class="card w-full sm:w-2/3 lg:w-1/2 xl:w-1/4 h-1/2 min-h-fit flex flex-col justify-center items-center bg-base-100 rounded-lg shadow-xl">
    {{-- Login failed toast --}}
    <div x-data="{ display: $wire.entangle('showFailedToast') }" class="toast toast-top toast-center">
        <div x-show="display" class="alert alert-error">
            <p>Invalid email or password.</p>
            <button @click="display = false" type="button" class="btn btn-circle btn-ghost btn-xs">
                <x-ionicon-close class="h-4 w-4"/>
            </button>
        </div>
    </div>

    <h1 class="pb-8 pt-5 lg:pt-0 xl:pt-0 text-center text-4xl brand-name-font">YUGUMO</h1>

    {{-- Login form --}}
    <x-form wire:submit="loginAttempt" class="w-2/3 flex flex-col justify-around items-center mb-8">

        {{-- Email input --}}
        <label class="w-full floating-label">
            <span>Email</span>
            <x-input
                    wire:model="email"
                    name="email" type="email"
                    placeholder="Email"
                    class="input w-full"
                    required
            />
        </label>
        @error('email')<p class="w-full text-start text-error">{{ $message }}</p>@enderror

        {{-- Password input --}}
        <label class="w-full floating-label mt-2">
            <span>Password</span>
            <x-password
                    wire:model="password"
                    name="password"
                    placeholder="Password"
                    class="input w-full"
                    required
            />
        </label>
        @error('password')<p class="w-full text-start text-error">{{ $message }}</p>@enderror

        {{-- Submit button --}}
        <button type="submit" class="w-full btn btn-primary mt-4">Log in</button>

    </x-form>
</div>
