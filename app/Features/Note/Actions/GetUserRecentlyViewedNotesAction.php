<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Models\Note;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

readonly class GetUserRecentlyViewedNotesAction
{
    /**
     * @return Collection<int, Note>
     */
    public function handle(?User $user = null, int $count = 6): Collection
    {
        $user ??= request()->user();

        return Note::query()
            ->where(Note::USER_ID, $user?->id ?? '')
            ->whereNotNull(Note::LAST_VIEWED_AT)
            ->orderByDesc(Note::LAST_VIEWED_AT)
            ->take($count)
            ->get();
    }
}
