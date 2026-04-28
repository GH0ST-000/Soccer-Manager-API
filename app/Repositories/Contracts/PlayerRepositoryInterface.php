<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Player;
use Illuminate\Database\Eloquent\Collection;

interface PlayerRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Player;

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function insertMany(array $rows): void;

    public function find(int $playerId): ?Player;

    public function lockById(int $playerId): ?Player;

    /**
     * @return Collection<int, Player>
     */
    public function forTeam(int $teamId): Collection;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Player $player, array $attributes): Player;
}
