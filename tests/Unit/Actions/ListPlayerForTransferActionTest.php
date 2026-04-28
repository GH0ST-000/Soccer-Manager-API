<?php

declare(strict_types=1);

use App\Actions\ListPlayerForTransferAction;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Validation\ValidationException;

it('lists a player and prevents duplicate active listings', function (): void {
    $team = Team::factory()->create();
    $player = Player::factory()->create(['team_id' => $team->id]);

    $action = app(ListPlayerForTransferAction::class);

    $listing = $action->execute($player, 1_000_000);
    expect($listing->player_id)->toBe($player->id);
    expect($listing->asking_price)->toBe(1_000_000);

    $action->execute($player, 2_000_000);
})->throws(ValidationException::class);
