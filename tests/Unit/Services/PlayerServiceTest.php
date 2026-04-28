<?php

declare(strict_types=1);

use App\Models\Player;
use App\Models\Team;
use App\Services\PlayerService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

it('lists players for a team and finds individual players', function (): void {
    $team = Team::factory()->create();
    Player::factory()->count(3)->create(['team_id' => $team->id]);

    /** @var PlayerService $service */
    $service = app(PlayerService::class);

    expect($service->listForTeam($team))->toHaveCount(3);

    $player = Player::query()->first();
    expect($service->find($player->id)->id)->toBe($player->id);
});

it('throws when a player is missing', function (): void {
    app(PlayerService::class)->find(999999);
})->throws(ModelNotFoundException::class);

it('updates a player', function (): void {
    $team = Team::factory()->create();
    $player = Player::factory()->create(['team_id' => $team->id]);

    $updated = app(PlayerService::class)->update($player, ['first_name' => 'New']);

    expect($updated->first_name)->toBe('New');
});
