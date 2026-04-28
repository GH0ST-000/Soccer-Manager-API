<?php

declare(strict_types=1);

use App\Enums\PlayerPosition;
use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\Team;
use App\Models\Transaction;
use App\Models\TransferListing;
use App\Models\User;

it('exposes the user-team relationship and casts', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    expect($user->team?->id)->toBe($team->id)
        ->and($team->user->id)->toBe($user->id)
        ->and($team->budget)->toBeInt();
});

it('exposes player relationships, casts, and active listing accessor', function (): void {
    $team = Team::factory()->create();
    $player = Player::factory()->create([
        'team_id' => $team->id,
        'position' => PlayerPosition::Attacker,
    ]);

    expect($player->team->id)->toBe($team->id)
        ->and($team->players)->toHaveCount(1)
        ->and($player->position)->toBeInstanceOf(PlayerPosition::class)
        ->and($player->age)->toBeInt()
        ->and($player->market_value)->toBeInt()
        ->and($player->activeListing)->toBeNull();

    $listing = TransferListing::factory()->active()->create([
        'player_id' => $player->id,
        'seller_team_id' => $team->id,
    ]);

    expect($player->fresh()?->activeListing?->id)->toBe($listing->id);
});

it('exposes transfer listing relationships and status casts', function (): void {
    $team = Team::factory()->create();
    $player = Player::factory()->create(['team_id' => $team->id]);
    $listing = TransferListing::factory()->active()->create([
        'player_id' => $player->id,
        'seller_team_id' => $team->id,
    ]);

    expect($listing->player->id)->toBe($player->id)
        ->and($listing->sellerTeam->id)->toBe($team->id)
        ->and($listing->status)->toBe(TransferListingStatus::Active)
        ->and($listing->asking_price)->toBeInt();
});

it('exposes transaction relationships and integer casts', function (): void {
    $seller = Team::factory()->create();
    $buyer = Team::factory()->create();
    $player = Player::factory()->create(['team_id' => $seller->id]);
    $transaction = Transaction::factory()->create([
        'player_id' => $player->id,
        'seller_team_id' => $seller->id,
        'buyer_team_id' => $buyer->id,
    ]);

    expect($transaction->player->id)->toBe($player->id)
        ->and($transaction->sellerTeam->id)->toBe($seller->id)
        ->and($transaction->buyerTeam->id)->toBe($buyer->id)
        ->and($transaction->price)->toBeInt()
        ->and($transaction->old_value)->toBeInt()
        ->and($transaction->new_value)->toBeInt();
});
