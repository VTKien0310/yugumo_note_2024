<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Models\Note;
use App\Features\Note\Queries\GetNoteByUserIdAndBookmarkStatusQuery;
use App\Features\User\Models\User;

readonly class CountUserBookmarkedNoteAction
{
    public function __construct(
        private GetNoteByUserIdAndBookmarkStatusQuery $getNoteByUserIdAndBookmarkStatusQuery
    ) {}

    public function handle(User $user): int
    {
        return $this->getNoteByUserIdAndBookmarkStatusQuery->handle(
            builder: Note::query(),
            userId: $user->id,
            bookmarked: true
        )->count();
    }
}
