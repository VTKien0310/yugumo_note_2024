<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Models\Note;
use App\Features\Note\Queries\GetNoteByUserIdAndBookmarkStatusQuery;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

readonly class GetUserBookmarkedNotesAction
{
    public function __construct(
        private GetNoteByUserIdAndBookmarkStatusQuery $getNoteByUserIdAndBookmarkStatusQuery
    ) {}

    /**
     * @return Collection<int, Note>
     */
    public function handle(User $user): Collection
    {
        return $this->getNoteByUserIdAndBookmarkStatusQuery
            ->handle(
                builder: Note::query(),
                userId: $user->id,
                bookmarked: true
            )
            ->orderByDesc(Note::CREATED_AT)
            ->take(Note::maxBookmarkedNotesPerUser())
            ->get();
    }
}
