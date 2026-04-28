<?php

declare(strict_types=1);

use App\Actions\CreateInitialTeamAction;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

it('returns a user team with players loaded', function (): void {
    $user = User::factory()->create();
    app(CreateInitialTeamAction::class)->execute($user);

    $team = app(TeamService::class)->getForUser($user->fresh());

    expect($team->players)->toHaveCount(20);
});

it('throws when a user has no team', function (): void {
    $user = User::factory()->create();

    app(TeamService::class)->getForUser($user);
})->throws(ModelNotFoundException::class);

it('updates a team', function (): void {
    $user = User::factory()->create();
    app(CreateInitialTeamAction::class)->execute($user);

    /** @var TeamService $service */
    $service = app(TeamService::class);
    $team = $service->getForUser($user->fresh());

    $updated = $service->update($team, ['name' => 'Updated']);

    expect($updated->name)->toBe('Updated');
});
