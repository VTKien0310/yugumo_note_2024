<?php

namespace App\Http\Profile;

use App\Extendables\Core\Http\Controllers\WebController;
use Illuminate\Contracts\View\View;

class ProfileController extends WebController
{
    public function show(): View
    {
        return view('pages.profile.profile-page');
    }
}
