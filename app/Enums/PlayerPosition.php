<?php

declare(strict_types=1);

namespace App\Enums;

enum PlayerPosition: string
{
    case Goalkeeper = 'goalkeeper';
    case Defender = 'defender';
    case Midfielder = 'midfielder';
    case Attacker = 'attacker';

    /**
     * Default count of players per position when generating an initial team roster.
     *
     * @return array<string, int>
     */
    public static function initialRosterDistribution(): array
    {
        return [
            self::Goalkeeper->value => 3,
            self::Defender->value => 6,
            self::Midfielder->value => 6,
            self::Attacker->value => 5,
        ];
    }
}
