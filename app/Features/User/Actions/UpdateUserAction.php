<?php

namespace App\Features\User\Actions;

use App\Features\User\Commands\UpdateUserCommand;
use App\Features\User\Models\User;

readonly class UpdateUserAction
{
    public function __construct(
        private UpdateUserCommand $updateUserCommand,
    ) {}

    public function handle(User $user, array $data): User
    {
        return $this->updateUserCommand->handle($user, $data);
    }
}
