<?php

namespace App\Features\Note\Authorizers;

use App\Features\Note\Models\Note;
use App\Features\User\Models\User;
use Illuminate\Http\Response;

readonly class ManageNoteAuthorizer
{
    public function handle(Note $note, ?User $user = null): void
    {
        $user = $user ?? request()->user();

        $unauthorized = $user === null || $user->id !== $note->user_id;

        abort_if($unauthorized, Response::HTTP_FORBIDDEN);
    }
}
