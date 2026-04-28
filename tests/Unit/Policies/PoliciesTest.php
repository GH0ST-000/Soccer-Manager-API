<?php

declare(strict_types=1);

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Policies\PlayerPolicy;
use App\Policies\TeamPolicy;

it('grants and denies team actions based on ownership', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $other = User::factory()->create();
    $otherTeam = Team::factory()->create(['user_id' => $other->id]);

    $policy = new TeamPolicy;

    expect($policy->view($user, $team))->toBeTrue()
        ->and($policy->update($user, $team))->toBeTrue()
        ->and($policy->view($user, $otherTeam))->toBeFalse()
        ->and($policy->update($user, $otherTeam))->toBeFalse();
});

it('grants and denies player actions based on team ownership', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $player = Player::factory()->create(['team_id' => $team->id]);

    $policy = new PlayerPolicy;

    expect($policy->update($user, $player))->toBeTrue()
        ->and($policy->listForTransfer($user, $player))->toBeTrue()
        ->and($policy->cancelTransfer($user, $player))->toBeTrue();

    $stranger = User::factory()->create();
    expect($policy->update($stranger, $player))->toBeFalse()
        ->and($policy->listForTransfer($stranger, $player))->toBeFalse()
        ->and($policy->cancelTransfer($stranger, $player))->toBeFalse();
});

it('denies player actions for a user without a team', function (): void {
    $user = User::factory()->create();
    $otherTeam = Team::factory()->create();
    $player = Player::factory()->create(['team_id' => $otherTeam->id]);

    expect((new PlayerPolicy)->update($user, $player))->toBeFalse();
});
