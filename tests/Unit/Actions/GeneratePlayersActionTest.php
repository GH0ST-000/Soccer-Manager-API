<?php

declare(strict_types=1);

use App\Actions\GeneratePlayersAction;
use App\Models\Player;
use App\Models\Team;

it('generates exactly 20 players with the required position distribution', function (): void {
    $team = Team::factory()->create();

    app(GeneratePlayersAction::class)->execute($team);

    $players = Player::query()->where('team_id', $team->id)->get();
    expect($players)->toHaveCount(20);

    $byPosition = $players->groupBy(fn (Player $p) => $p->position->value);
    expect($byPosition->get('goalkeeper'))->toHaveCount(3);
    expect($byPosition->get('defender'))->toHaveCount(6);
    expect($byPosition->get('midfielder'))->toHaveCount(6);
    expect($byPosition->get('attacker'))->toHaveCount(5);

    foreach ($players as $player) {
        expect($player->market_value)->toBe(GeneratePlayersAction::DEFAULT_MARKET_VALUE);
        expect($player->age)->toBeGreaterThanOrEqual(GeneratePlayersAction::MIN_AGE);
        expect($player->age)->toBeLessThanOrEqual(GeneratePlayersAction::MAX_AGE);
    }
});
