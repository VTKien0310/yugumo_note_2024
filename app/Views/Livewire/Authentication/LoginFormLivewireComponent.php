<?php

namespace App\Views\Livewire\Authentication;

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

    public function loginAttempt()
    {
        $validated = $this->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        session()->flash('login-failed', 'Invalid email or password.');
    }
}
