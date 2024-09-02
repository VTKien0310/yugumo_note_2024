<?php

namespace App\Http\Authentication;

use App\Extendables\Core\Http\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticationController extends WebController
{
    /**
     * GET /auth/login
     */
    public function loginPage(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('note.list');
        }

        return view('modules.authentication.pages.login-page');
    }
}
