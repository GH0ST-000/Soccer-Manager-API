<?php

declare(strict_types=1);

use App\Models\User;
use App\Repositories\TeamRepository;

it('creates, finds, updates, and locks teams; adjusts budgets', function (): void {
    $repo = new TeamRepository;
    $user = User::factory()->create();

    $team = $repo->create([
        'user_id' => $user->id,
        'name' => 'A',
        'country' => 'X',
        'budget' => 1_000_000,
    ]);

    expect($repo->findForUser($user->id)?->id)->toBe($team->id)
        ->and($repo->findForUser(99999))->toBeNull();

    $updated = $repo->update($team, ['name' => 'B']);
    expect($updated->name)->toBe('B');

    $repo->adjustBudget($team, 500_000);
    expect($team->fresh()?->budget)->toBe(1_500_000)
        ->and($repo->lockById($team->id)?->id)->toBe($team->id)
        ->and($repo->lockById(99999))->toBeNull();

});
