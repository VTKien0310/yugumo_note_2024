<?php

namespace App\Features\Note\Actions;

use App\Extendables\Core\Models\Queries\PaginateQuery;
use App\Features\Note\Models\Note;
use App\Features\Note\Queries\IndexNoteQuery;
use App\Features\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class ListNoteOfUserAction
{
    public function __construct(
        private IndexNoteQuery $indexNoteQuery,
        private PaginateQuery $paginateQuery
    ) {}

    public function handle(User $user): LengthAwarePaginator
    {
        $query = Note::query()->where(Note::USER_ID, $user->id);

        $query = $this->indexNoteQuery->handle($query);

        return $this->paginateQuery->handle($query);
    }
}
