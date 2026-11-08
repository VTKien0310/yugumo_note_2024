<?php

use App\Http\Web\Routes\Authentication\WebAuthenticationController;
use Tests\Factories\UserFactory;

describe(WebAuthenticationController::class, function () {
    it('allows guest to access login page', function () {
        $response = $this->get(route('auth.login'));

        $response->assertOk();
    });

    it('redirects unauthenticated user accessing root to login page', function () {
        $response = $this->get('/');

        $response->assertRedirect(route('auth.login'));
    });

    it('allows authenticated user to log out', function () {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)
            ->delete(route('auth.logout'));

        $response->assertRedirect(route('auth.login'));
        $this->assertGuest();
    });
});
