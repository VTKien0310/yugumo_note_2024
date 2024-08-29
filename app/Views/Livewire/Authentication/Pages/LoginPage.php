<?php

namespace App\Views\Livewire\Authentication\Pages;

use Livewire\Component;

class LoginPage extends Component
{
    public function render()
    {
        return view('livewire.authentication.pages.login-page')->layout('components.layouts.master-layout');
    }
}
