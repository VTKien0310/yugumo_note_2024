<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Models\Note;
use App\Features\Note\ValueObjects\NoteCountByTypeStatisticsValueObject;
use App\Features\Note\ValueObjects\NoteCountStatisticsValueObject;
use App\Features\NoteType\Actions\GetAllNoteTypeAction;
use App\Features\NoteType\Models\NoteType;
use App\Features\User\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class GetUserNotesCountStatisticsAction
{
    public function __construct(
        private GetAllNoteTypeAction $getAllNoteTypeAction
    ) {}

    public function handle(User $user): NoteCountStatisticsValueObject
    {
        $typeId = Note::TYPE_ID;
        $count = 'count';

        /** @var Collection<int, object{type_id:int,count:int}> $queryResult * */
        $queryResult = DB::table(Note::table())
            ->where(Note::USER_ID, $user->id)
            ->whereNull(Note::DELETED_AT)
            ->groupBy(Note::TYPE_ID)
            ->orderBy(Note::TYPE_ID)
            ->selectRaw("$typeId, count(*) as $count")
            ->get();

        $encapsulateNoteTypeStatQueryResult = function (NoteType $noteType) use (
            $queryResult,
            $typeId,
            $count
        ): NoteCountByTypeStatisticsValueObject {
            $noteTypeStat = $queryResult->firstWhere($typeId, $noteType->id);

            return new NoteCountByTypeStatisticsValueObject(
                type: $noteType->name,
                count: $noteTypeStat
                    ? $noteTypeStat->{$count} ?? 0
                    : 0
            );
        };

        $countByType = $this->getAllNoteTypeAction->handle()->map($encapsulateNoteTypeStatQueryResult);

        $totalCount = $queryResult->sum($count);

        return new NoteCountStatisticsValueObject(
            total: $totalCount,
            byType: $countByType,
        );
    }
}
