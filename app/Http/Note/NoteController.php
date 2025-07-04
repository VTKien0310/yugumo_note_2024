<?php

namespace App\Http\Note;

use App\Extendables\Core\Http\Controllers\WebController;
use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Actions\GetUserBookmarkedNotesAction;
use App\Features\Note\Actions\GetUserNotesCountStatisticsAction;
use App\Features\Note\Actions\GetUserRecentlyViewedNotesAction;
use App\Features\Note\Actions\GetUserTrendingNotesAction;
use App\Features\Note\Actions\MakeNoteListDisplayDataAction;
use App\Features\Note\Actions\UpdateNoteAction;
use App\Features\Note\Actions\ViewNoteAction;
use App\Features\Note\Authorizers\ManageNoteAuthorizer;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NoteController extends WebController
{
    /**
     * GET /
     */
    public function home(
        Request $request,
        GetUserRecentlyViewedNotesAction $getUserRecentlyViewedNotesAction,
        GetUserTrendingNotesAction $getUserTrendingNotesAction,
        GetUserBookmarkedNotesAction $getUserBookmarkedNotesAction,
        MakeNoteListDisplayDataAction $makeNoteListDisplayDataAction,
        GetUserNotesCountStatisticsAction $getUserNotesCountStatisticsAction
    ): View {
        $requestUser = $request->user();

        $recentlyViewedNotes = $getUserRecentlyViewedNotesAction
            ->handle($requestUser)
            ->map(fn (Note $note) => $makeNoteListDisplayDataAction->handle($note))
            ->all();

        $trendingNotes = $getUserTrendingNotesAction
            ->handle($requestUser)
            ->map(fn (Note $note) => $makeNoteListDisplayDataAction->handle($note))
            ->all();

        $bookmarkedNotes = $getUserBookmarkedNotesAction
            ->handle($requestUser)
            ->map(fn (Note $note) => $makeNoteListDisplayDataAction->handle($note))
            ->all();

        $notesCountStatistics = $getUserNotesCountStatisticsAction->handle($requestUser);

        return view('pages.note.home-note-page',
            compact('recentlyViewedNotes', 'trendingNotes', 'bookmarkedNotes', 'notesCountStatistics')
        );
    }

    /**
     * GET /notes
     */
    public function index(): View
    {
        return view('pages.note.list-note-page');
    }

    /**
     * GET /notes/create
     */
    public function create(): View
    {
        return view('pages.note.create-note-page');
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

        return view('pages.note.edit-note-page', compact('note', 'noteType'));
    }

    /**
     * PUT /notes/:id/remove-bookmark
     */
    public function removeBookmark(
        Note $note,
        Request $request,
        ManageNoteAuthorizer $manageNoteAuthorizer,
        UpdateNoteAction $updateNoteAction
    ): RedirectResponse {
        $manageNoteAuthorizer->handle($note, $request->user());

        $updateNoteAction->handle($note, [
            Note::BOOKMARKED => BoolIntValueEnum::FALSE,
        ]);

        return redirect()
            ->route('notes.home')
            ->with('remove-bookmark-success', 'Note bookmark removed successfully.');
    }
}
