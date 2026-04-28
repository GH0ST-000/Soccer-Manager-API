<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Unit');

expect()->extend('toBeOne', fn () => $this->toBe(1));

/**
 * @return array{Authorization: string, Accept: string}
 */
function authHeaders(User $user): array
{
    Auth::forgetGuards();

    return [
        'Authorization' => 'Bearer '.JWTAuth::fromUser($user),
        'Accept' => 'application/json',
    ];
}

/**
 * @return array{Accept: string}
 */
function jsonHeaders(): array
{
    return ['Accept' => 'application/json'];
}
