<?php

namespace App\Features\User\Commands;

use App\Features\User\Models\User;

class UpdateUserCommand
{
    public function handle(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh();
    }
}
