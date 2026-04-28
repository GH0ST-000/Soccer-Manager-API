<?php

declare(strict_types=1);

use App\Models\Player;
use App\Models\Team;
use App\Repositories\TransactionRepository;

it('creates a transaction record', function (): void {
    $repo = new TransactionRepository;
    $seller = Team::factory()->create();
    $buyer = Team::factory()->create();
    $player = Player::factory()->create(['team_id' => $seller->id]);

    $transaction = $repo->create([
        'player_id' => $player->id,
        'seller_team_id' => $seller->id,
        'buyer_team_id' => $buyer->id,
        'price' => 1_000_000,
        'old_value' => 1_000_000,
        'new_value' => 1_500_000,
    ]);

    expect($transaction->price)->toBe(1_000_000)
        ->and($transaction->buyer_team_id)->toBe($buyer->id);
});
