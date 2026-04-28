<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PlayerPosition;
use App\Models\Team;
use App\Repositories\Contracts\PlayerRepositoryInterface;
use Faker\Factory as FakerFactory;
use Faker\Generator;

final readonly class GeneratePlayersAction
{
    public const int DEFAULT_MARKET_VALUE = 1_000_000;

    public const int MIN_AGE = 18;

    public const int MAX_AGE = 40;

    private Generator $faker;

    public function __construct(
        private PlayerRepositoryInterface $players,
    ) {
        $this->faker = FakerFactory::create();
    }

    public function execute(Team $team): void
    {
        $now = now();
        $rows = [];

        foreach (PlayerPosition::initialRosterDistribution() as $position => $count) {
            for ($i = 0; $i < $count; $i++) {
                $rows[] = [
                    'team_id' => $team->id,
                    'first_name' => $this->faker->firstName(),
                    'last_name' => $this->faker->lastName(),
                    'country' => $this->faker->country(),
                    'position' => $position,
                    'age' => random_int(self::MIN_AGE, self::MAX_AGE),
                    'market_value' => self::DEFAULT_MARKET_VALUE,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        $this->players->insertMany($rows);
    }
}
