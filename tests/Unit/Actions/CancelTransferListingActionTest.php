<?php

declare(strict_types=1);

use App\Actions\CancelTransferListingAction;
use App\Actions\ListPlayerForTransferAction;
use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Validation\ValidationException;

it('cancels an existing active listing', function (): void {
    $team = Team::factory()->create();
    $player = Player::factory()->create(['team_id' => $team->id]);

    app(ListPlayerForTransferAction::class)->execute($player, 1_000_000);

    $cancelled = app(CancelTransferListingAction::class)->execute($player);

    expect($cancelled->status)->toBe(TransferListingStatus::Cancelled);
});

it('throws when there is no active listing for the player', function (): void {
    $team = Team::factory()->create();
    $player = Player::factory()->create(['team_id' => $team->id]);

    app(CancelTransferListingAction::class)->execute($player);
})->throws(ValidationException::class);
