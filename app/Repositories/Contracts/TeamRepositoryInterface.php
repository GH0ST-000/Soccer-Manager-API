<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Team;

interface TeamRepositoryInterface
{
    /**
     * @param  array{user_id: int, name: string, country: string, budget?: int}  $attributes
     */
    public function create(array $attributes): Team;

    public function findForUser(int $userId): ?Team;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Team $team, array $attributes): Team;

    public function adjustBudget(Team $team, int $delta): Team;

    public function lockById(int $teamId): ?Team;
}
