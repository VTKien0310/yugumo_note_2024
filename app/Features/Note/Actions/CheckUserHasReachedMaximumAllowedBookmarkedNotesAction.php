<?php

namespace App\Features\Note\Actions;

use App\Features\User\Models\User;

readonly class CheckUserHasReachedMaximumAllowedBookmarkedNotesAction
{
    private const int MAXIMUM_ALLOWED_BOOKMARKED = 10;

    public function __construct(
        private CountUserBookmarkedNoteAction $countUserBookmarkedNoteAction,
    ) {}

    public function handle(User $user): bool
    {
        $userBookmarkedNote = $this->countUserBookmarkedNoteAction->handle($user);

        return $userBookmarkedNote >= static::MAXIMUM_ALLOWED_BOOKMARKED;
    }
}
