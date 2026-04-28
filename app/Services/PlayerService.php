<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Player;
use App\Models\Team;
use App\Repositories\Contracts\PlayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class PlayerService
{
    public function __construct(
        private PlayerRepositoryInterface $players,
    ) {}

    /**
     * @return Collection<int, Player>
     */
    public function listForTeam(Team $team): Collection
    {
        return $this->players->forTeam($team->id);
    }

    public function find(int $playerId): Player
    {
        $player = $this->players->find($playerId);

        if (! $player instanceof Player) {
            $message = __('soccer.player.not_found');
            throw new ModelNotFoundException(is_string($message) ? $message : 'Player not found.');
        }

        return $player;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Player $player, array $attributes): Player
    {
        return $this->players->update($player, $attributes);
    }
}
