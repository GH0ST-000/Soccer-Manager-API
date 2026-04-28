<?php

declare(strict_types=1);

use App\Enums\PlayerPosition;

it('has the correct initial roster distribution totalling 20 players', function (): void {
    $distribution = PlayerPosition::initialRosterDistribution();

    expect($distribution)->toBe([
        'goalkeeper' => 3,
        'defender' => 6,
        'midfielder' => 6,
        'attacker' => 5,
    ])
        ->and(array_sum($distribution))->toBe(20);

});

it('exposes a case for every position', function (): void {
    expect(PlayerPosition::cases())->toHaveCount(4);
});
