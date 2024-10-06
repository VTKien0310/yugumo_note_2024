<?php

namespace App\Features\Note\Authorizers;

use App\Features\Note\Models\Note;
use App\Features\User\Models\User;

readonly class ViewNoteAuthorizer
{
    public function handle(User $user, Note $note): void
    {
        abort_if($user->id !== $note->user_id, 403);
    }
}
