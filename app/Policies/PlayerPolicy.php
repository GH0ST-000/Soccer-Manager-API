<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Player;
use App\Models\Team;
use App\Models\User;

final class PlayerPolicy
{
    public function update(User $user, Player $player): bool
    {
        return $this->ownsPlayer($user, $player);
    }

    public function listForTransfer(User $user, Player $player): bool
    {
        return $this->ownsPlayer($user, $player);
    }

    public function cancelTransfer(User $user, Player $player): bool
    {
        return $this->ownsPlayer($user, $player);
    }

    private function ownsPlayer(User $user, Player $player): bool
    {
        $team = $user->team;

        if (! $team instanceof Team) {
            return false;
        }

        return $player->team_id === $team->id;
    }
}
