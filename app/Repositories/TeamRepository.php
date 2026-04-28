<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Team;
use App\Repositories\Contracts\TeamRepositoryInterface;

final class TeamRepository implements TeamRepositoryInterface
{
    public function create(array $attributes): Team
    {
        return Team::query()->create($attributes);
    }

    public function findForUser(int $userId): ?Team
    {
        return Team::query()->where('user_id', $userId)->first();
    }

    public function update(Team $team, array $attributes): Team
    {
        $team->fill($attributes);
        $team->save();

        return $team;
    }

    public function adjustBudget(Team $team, int $delta): Team
    {
        $team->budget += $delta;
        $team->save();

        return $team;
    }

    public function lockById(int $teamId): ?Team
    {
        return Team::query()->where('id', $teamId)->lockForUpdate()->first();
    }
}
