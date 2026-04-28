<?php

declare(strict_types=1);

use App\Actions\RegisterUserAction;
use App\Models\User;

it('registers a user inside a transaction and creates an initial team', function (): void {
    $user = app(RegisterUserAction::class)->execute([
        'name' => 'Tester',
        'email' => 'tester@example.com',
        'password' => 'password',
    ]);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->team)->not->toBeNull();
    expect($user->team->players()->count())->toBe(20);
});
