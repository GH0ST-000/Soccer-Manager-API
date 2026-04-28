<?php

declare(strict_types=1);

use App\Repositories\UserRepository;

it('creates and finds users by email', function (): void {
    $repo = new UserRepository;

    $user = $repo->create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    expect($user->email)->toBe('test@example.com')
        ->and($repo->findByEmail('test@example.com')?->id)->toBe($user->id)
        ->and($repo->findByEmail('missing@example.com'))->toBeNull();
});
