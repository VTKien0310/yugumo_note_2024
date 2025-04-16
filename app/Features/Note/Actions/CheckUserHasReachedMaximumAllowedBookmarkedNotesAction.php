<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Models\Note;
use App\Features\User\Models\User;

readonly class CheckUserHasReachedMaximumAllowedBookmarkedNotesAction
{
    public function __construct(
        private CountUserBookmarkedNoteAction $countUserBookmarkedNoteAction,
    ) {}

    public function handle(User $user): bool
    {
        $userBookmarkedNote = $this->countUserBookmarkedNoteAction->handle($user);

        return $userBookmarkedNote >= Note::maxBookmarkedNotesPerUser();
    }
}
