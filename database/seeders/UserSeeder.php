<?php

namespace Database\Seeders;

use App\Features\User\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // flush all users before seed new data
        User::query()->forceDelete();

        $users = [
            [
                'name' => 'Ricky',
                'email' => 'trgkien.vu@gmail.com',
                'password' => 'Password1!',
            ],
            [
                'name' => 'tomo',
                'email' => 'shiorihanazawa@gmail.com',
                'password' => 'Password1!',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
