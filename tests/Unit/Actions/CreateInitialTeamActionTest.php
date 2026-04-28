<?php

declare(strict_types=1);

use App\Actions\CreateInitialTeamAction;
use App\Models\User;

it('creates a team with default budget and a 20-player roster', function (): void {
    $user = User::factory()->create(['name' => 'Owner']);

    $team = app(CreateInitialTeamAction::class)->execute($user);

    expect($team->user_id)->toBe($user->id);
    expect($team->budget)->toBe(CreateInitialTeamAction::DEFAULT_BUDGET);
    expect($team->name)->toContain('Owner');
    expect($team->players()->count())->toBe(20);
});
