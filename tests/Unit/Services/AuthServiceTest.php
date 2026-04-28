<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\AuthService;

it('registers a user and returns a JWT token', function (): void {
    $service = app(AuthService::class);

    $result = $service->register([
        'name' => 'Tester',
        'email' => 'tester@example.com',
        'password' => 'password',
    ]);

    expect($result['user'])->toBeInstanceOf(User::class)
        ->and($result['token'])->toBeString()->not->toBe('');
});

it('logs a user in with valid credentials', function (): void {
    User::factory()->create([
        'email' => 'user@example.com',
        'password' => 'correct-password',
    ]);

    $result = app(AuthService::class)->login([
        'email' => 'user@example.com',
        'password' => 'correct-password',
    ]);

    expect($result)->not->toBeNull()
        ->and($result['token'])->toBeString();
});

it('returns null on invalid login credentials', function (): void {
    $result = app(AuthService::class)->login([
        'email' => 'no@example.com',
        'password' => 'whatever',
    ]);

    expect($result)->toBeNull();
});

it('exposes the authenticated user and supports logout', function (): void {
    /** @var AuthService $service */
    $service = app(AuthService::class);

    $registered = $service->register([
        'name' => 'A',
        'email' => 'a@example.com',
        'password' => 'password',
    ]);

    $user = $service->user();
    expect($user?->getAuthIdentifier())->toBe($registered['user']->id);

    $service->logout();
    expect($service->user())->toBeNull();
});
