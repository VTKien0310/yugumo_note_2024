<?php

namespace App\Http\Note;

use App\Extendables\Core\Http\Controllers\WebController;
use App\Features\Note\Actions\ViewNoteAction;
use App\Features\Note\Authorizers\ManageNoteAuthorizer;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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

    /**
     * GET /notes/:id
     */
    public function show(
        Note $note,
        Request $request,
        ManageNoteAuthorizer $manageNoteAuthorizer,
        ViewNoteAction $viewNoteAction
    ): View {
        $manageNoteAuthorizer->handle($note, $request->user());

        $note = $viewNoteAction->handle($note);

        $noteType = $note->type;

        match ($noteType->id) {
            NoteTypeEnum::CHECKLIST->value => $note->load(Note::RELATION_CHECKLIST_CONTENT),
            default => $note->load(Note::RELATION_TEXT_CONTENT)
        };

        return view('modules.note.pages.edit-note-page', compact('note', 'noteType'));
    }
}
