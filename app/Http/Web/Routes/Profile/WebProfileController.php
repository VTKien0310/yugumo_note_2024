<?php

namespace App\Http\Web\Routes\Profile;

use App\Extendables\Core\Http\Controllers\WebController;
use Illuminate\Contracts\View\View;

class WebProfileController extends WebController
{
    public function show(): View
    {
        return view('pages.profile.profile-page');
    }
}
