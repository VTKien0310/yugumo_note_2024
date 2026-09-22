<?php

namespace App\Http\Bff\Routes\Authentication;

use App\Extendables\Core\Http\Controllers\ApiController;
use App\Extendables\Core\Http\Response\Responder;
use App\Features\User\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BffAuthenticationController extends ApiController
{
    public function __construct(
        private readonly Responder $responder
    ) {}

    /**
     * POST /bff/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            User::EMAIL => 'required|email',
            User::PASSWORD => 'required',
        ]);

        $authenticateSuccess = Auth::attempt($credentials, true);

        if (! $authenticateSuccess) {
            return $this->responder->responseUnauthenticated();
        }

        $request->session()->regenerate();

        return $this->responder->responseRawContent([
            'user' => $this->userData($request->user()),
        ]);
    }

    /**
     * GET /bff/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        return $this->responder->responseRawContent([
            'user' => $this->userData($request->user()),
        ]);
    }

    /**
     * DELETE /bff/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->responder->responseNoContent();
    }

    private function userData(User $user): array
    {
        return [
            User::ID => $user->id,
            User::NAME => $user->name,
            User::EMAIL => $user->email,
        ];
    }
}
