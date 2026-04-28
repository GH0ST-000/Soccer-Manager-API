<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->auth->register($request->credentials());

        return response()->json([
            'message' => __('soccer.auth.registered'),
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => 'bearer',
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->auth->login($request->credentials());

        if ($result === null) {
            return response()->json([
                'message' => __('soccer.auth.invalid_credentials'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'message' => __('soccer.auth.logged_in'),
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => 'bearer',
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->auth->logout();

        return response()->json([
            'message' => __('soccer.auth.logged_out'),
        ]);
    }
}
