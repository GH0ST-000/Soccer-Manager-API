<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Player;
use App\Repositories\Contracts\PlayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class PlayerRepository implements PlayerRepositoryInterface
{
    public function create(array $attributes): Player
    {
        return Player::query()->create($attributes);
    }

    public function insertMany(array $rows): void
    {
        if ($rows === []) {
            return;
        }

        Player::query()->insert($rows);
    }

    public function find(int $playerId): ?Player
    {
        return Player::query()->find($playerId);
    }

    public function lockById(int $playerId): ?Player
    {
        return Player::query()->where('id', $playerId)->lockForUpdate()->first();
    }

    public function forTeam(int $teamId): Collection
    {
        return Player::query()->where('team_id', $teamId)->get();
    }

    public function update(Player $player, array $attributes): Player
    {
        $player->fill($attributes);
        $player->save();

        return $player;
    }
}
