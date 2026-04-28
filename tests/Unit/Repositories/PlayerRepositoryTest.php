<?php

declare(strict_types=1);

use App\Enums\PlayerPosition;
use App\Models\Team;
use App\Repositories\PlayerRepository;

it('creates, finds, locks, lists, updates, and bulk-inserts players', function (): void {
    $repo = new PlayerRepository;
    $team = Team::factory()->create();

    $player = $repo->create([
        'team_id' => $team->id,
        'first_name' => 'A',
        'last_name' => 'B',
        'country' => 'C',
        'position' => PlayerPosition::Attacker->value,
        'age' => 25,
        'market_value' => 1_000_000,
    ]);

    expect($repo->find($player->id)?->id)->toBe($player->id)
        ->and($repo->find(99999))->toBeNull()
        ->and($repo->lockById($player->id)?->id)->toBe($player->id)
        ->and($repo->lockById(99999))->toBeNull()
        ->and($repo->forTeam($team->id))->toHaveCount(1);

    $updated = $repo->update($player, ['first_name' => 'X']);
    expect($updated->first_name)->toBe('X');

    $now = now();
    $repo->insertMany([[
        'team_id' => $team->id,
        'first_name' => 'M',
        'last_name' => 'N',
        'country' => 'O',
        'position' => PlayerPosition::Defender->value,
        'age' => 30,
        'market_value' => 1_000_000,
        'created_at' => $now,
        'updated_at' => $now,
    ]]);

    expect($repo->forTeam($team->id))->toHaveCount(2);
});

it('insertMany returns early when given an empty list', function (): void {
    $repo = new PlayerRepository;
    $team = Team::factory()->create();

    $repo->insertMany([]);

    expect($repo->forTeam($team->id))->toHaveCount(0);
});
