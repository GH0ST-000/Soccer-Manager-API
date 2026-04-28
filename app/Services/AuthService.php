<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\RegisterUserAction;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTGuard;

final readonly class AuthService
{
    public function __construct(
        private RegisterUserAction $registerUser,
    ) {}

    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     * @return array{user: User, token: string}
     */
    public function register(array $attributes): array
    {
        $user = $this->registerUser->execute($attributes);

        $token = $this->guard()->login($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * @param  array{email: string, password: string}  $credentials
     * @return array{user: User, token: string}|null
     */
    public function login(array $credentials): ?array
    {
        $token = $this->guard()->attempt($credentials);

        if (! is_string($token) || $token === '') {
            return null;
        }

        /** @var User $user */
        $user = $this->guard()->user();

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(): void
    {
        $this->guard()->logout();
    }

    public function user(): ?Authenticatable
    {
        return $this->guard()->user();
    }

    private function guard(): JWTGuard
    {
        /** @var JWTGuard $guard */
        $guard = Auth::guard('api');

        return $guard;
    }
}
