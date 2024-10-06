<?php

namespace App\Http\Note;

use App\Extendables\Core\Http\Controllers\WebController;
use Illuminate\Contracts\View\View;

class NoteController extends WebController
{
    /**
     * GET /
     */
    public function home(): View
    {
        return view('modules.note.pages.home-note-page');
    }

    /**
     * GET /notes
     */
    public function index(): View
    {
        return view('modules.note.pages.list-note-page');
    }

    /**
     * GET /notes/create
     */
    public function create(): View
    {
        return view('modules.note.pages.create-note-page');
    }
}
