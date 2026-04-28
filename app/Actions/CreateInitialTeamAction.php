<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Team;
use App\Models\User;
use App\Repositories\Contracts\TeamRepositoryInterface;
use Faker\Factory as FakerFactory;

final readonly class CreateInitialTeamAction
{
    public const int DEFAULT_BUDGET = 5_000_000;

    public function __construct(
        private TeamRepositoryInterface $teams,
        private GeneratePlayersAction $generatePlayers,
    ) {}

    public function execute(User $user): Team
    {
        $faker = FakerFactory::create();

        $team = $this->teams->create([
            'user_id' => $user->id,
            'name' => $user->name."'s Team",
            'country' => $faker->country(),
            'budget' => self::DEFAULT_BUDGET,
        ]);

        $this->generatePlayers->execute($team);

        return $team;
    }
}
