<?php

use App\Features\Note\Actions\CreateNewNoteWithDefaultContentAction;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\NoteType\Models\NoteType;
use App\Features\User\Models\User;
use Database\Seeders\NoteTypeSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Factories\UserFactory;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case Bindings
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". You may change it
| using the "pest()" function to bind different classes or traits.
|
*/

pest()->in('Features')
    ->extend(TestCase::class)
    ->use(DatabaseTransactions::class);

pest()->in('Http')
    ->extend(TestCase::class)
    ->use(DatabaseTransactions::class);

pest()->in('Integration')
    ->extend(TestCase::class)
    ->use(DatabaseTransactions::class);

pest()->in('Architecture')
    ->extend(TestCase::class);

pest()->in('Extendables')
    ->extend(TestCase::class);

/*
|--------------------------------------------------------------------------
| Global Helper Functions
|--------------------------------------------------------------------------
*/

/**
 * Authenticates as the given user (or creates one) and attaches CSRF tokens for session/BFF requests.
 */
function actingAsUser(?User $user = null): TestCase
{
    $user ??= UserFactory::new()->create();
    $token = 'test-csrf-token';

    return test()->actingAs($user)
        ->withSession(['_token' => $token])
        ->withHeader('X-CSRF-TOKEN', $token);
}

/**
 * Creates a new note of the given type with default content for the given user.
 *
 * @param  array<string, mixed>  $extra
 */
function createNote(NoteTypeEnum $type, ?User $user = null, array $extra = []): Note
{
    $user ??= UserFactory::new()->create();

    // Ensure note types exist
    if (! NoteType::query()->where(NoteType::ID, $type->value)->exists()) {
        test()->seed(NoteTypeSeeder::class);
    }

    $noteType = NoteType::query()->findOrFail($type->value);

    return app(CreateNewNoteWithDefaultContentAction::class)->handle($user, $noteType);
}
