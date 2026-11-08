<?php

namespace Tests\Factories;

use App\Features\User\Models\User;
use Illuminate\Support\Str;
use Tests\TestFactory;

class UserFactory extends TestFactory
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function create(array $extra = []): User
    {
        return User::query()->create(array_merge([
            User::NAME => 'Test User',
            User::EMAIL => 'user-'.Str::uuid().'@example.com',
            User::PASSWORD => 'password',
        ], $extra));
    }
}
