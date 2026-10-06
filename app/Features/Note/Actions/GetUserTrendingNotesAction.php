<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Models\Note;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

readonly class GetUserTrendingNotesAction
{
    public function handle(?User $user = null, int $dayRange = 7, int $count = 6): Collection
    {
        $user ??= request()->user();

        $trendingNotesWithinDayRange = $this->getUserMostViewedNotesWithingDayRange(
            user: $user,
            dayRange: $dayRange,
            count: $count
        );
        if ($this->trendingNotesWithinDayRangeFulfillRequestedCount($trendingNotesWithinDayRange, $count)) {
            return $trendingNotesWithinDayRange;
        }

        return $this->fulfillRequestedCountWithTrendingNotesOfAllTime($user, $count, $trendingNotesWithinDayRange);
    }

    /**
     * @param  Collection<int, Note>  $trendingNotesWithinDayRange
     */
    private function trendingNotesWithinDayRangeFulfillRequestedCount(
        Collection $trendingNotesWithinDayRange,
        int $count
    ): bool {
        return $trendingNotesWithinDayRange->count() === $count;
    }

    /**
     * @param  Collection<int, Note>  $trendingNotesWithinDayRange
     * @return Collection<int, Note>
     */
    private function fulfillRequestedCountWithTrendingNotesOfAllTime(
        ?User $user,
        int $count,
        Collection $trendingNotesWithinDayRange
    ): Collection {
        $remainingCountToFulfill = $count - $trendingNotesWithinDayRange->count();

        $trendingNotesOfAllTime = $this->getMostViewedNotesOfAllTime(
            user: $user,
            count: $remainingCountToFulfill,
            excludeIds: $trendingNotesWithinDayRange->pluck(Note::ID)->toArray()
        );

        return $trendingNotesWithinDayRange->merge($trendingNotesOfAllTime);
    }

    /**
     * @return Collection<int, Note>
     */
    private function getUserMostViewedNotesWithingDayRange(?User $user, int $dayRange, int $count): Collection
    {
        return Note::query()
            ->where(Note::USER_ID, $user?->id ?? '')
            ->where(Note::LAST_VIEWED_AT, '>=', now()->subDays($dayRange))
            ->orderByDesc(Note::VIEWS)
            ->take($count)
            ->get();
    }

    /**
     * @param  string[]  $excludeIds
     * @return Collection<int, Note>
     */
    private function getMostViewedNotesOfAllTime(?User $user, int $count, array $excludeIds): Collection
    {
        return Note::query()
            ->where(Note::USER_ID, $user?->id ?? '')
            ->whereNotIn(Note::ID, $excludeIds)
            ->orderByDesc(Note::VIEWS)
            ->take($count)
            ->get();
    }
}
