<?php

namespace App\Http\Authentication;

use App\Extendables\Core\Http\Controllers\WebController;
use Illuminate\View\View;

class AuthenticationController extends WebController
{
    /**
     * GET /auth/login
     *
     * @return View
     */
    public function loginPage(): View
    {
        return view('modules.authentication.pages.login-page');
    }
}
