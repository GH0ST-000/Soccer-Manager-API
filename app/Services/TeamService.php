<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Repositories\Contracts\TeamRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class TeamService
{
    public function __construct(
        private TeamRepositoryInterface $teams,
    ) {}

    public function getForUser(User $user): Team
    {
        $team = $this->teams->findForUser($user->id);

        if (! $team instanceof Team) {
            $message = __('soccer.team.not_found');
            throw new ModelNotFoundException(is_string($message) ? $message : 'Team not found.');
        }

        return $team->load('players');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Team $team, array $attributes): Team
    {
        return $this->teams->update($team, $attributes);
    }
}
