<?php

namespace App\Views\Livewire\Authentication;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class LoginFormLivewireComponent extends Component
{
    public string $email = '';

    public string $password = '';

    public function render(): View
    {
        return view('livewire.authentication.login-form-livewire-component');
    }

    public function loginAttempt(): void
    {
        $credentials = $this->validatedData();

        $authenticateSuccess = Auth::attempt($credentials);

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
