<?php

declare(strict_types=1);

use App\Actions\CreateInitialTeamAction;
use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\Team;
use App\Models\Transaction;
use App\Models\TransferListing;
use App\Models\User;

beforeEach(function (): void {
    $this->seller = User::factory()->create();
    app(CreateInitialTeamAction::class)->execute($this->seller);
    $this->seller->refresh();

    $this->buyer = User::factory()->create();
    app(CreateInitialTeamAction::class)->execute($this->buyer);
    $this->buyer->refresh();

    /** @var Team $sellerTeam */
    $sellerTeam = $this->seller->team;
    /** @var Team $buyerTeam */
    $buyerTeam = $this->buyer->team;

    $this->sellerTeam = $sellerTeam;
    $this->buyerTeam = $buyerTeam;

    /** @var Player $player */
    $player = $sellerTeam->players()->first();
    $this->player = $player;

    $this->listing = TransferListing::factory()->active()->create([
        'player_id' => $player->id,
        'seller_team_id' => $sellerTeam->id,
        'asking_price' => 1_000_000,
    ]);
});

it('completes a purchase and updates ownership, budgets and value', function (): void {
    $sellerInitialBudget = $this->sellerTeam->budget;
    $buyerInitialBudget = $this->buyerTeam->budget;
    $oldValue = $this->player->market_value;

    $response = $this->withHeaders(authHeaders($this->buyer))
        ->postJson('/api/transfer-list/'.$this->listing->id.'/buy');

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'player_id', 'price', 'old_value', 'new_value'],
        ]);

    $this->sellerTeam->refresh();
    $this->buyerTeam->refresh();
    $this->player->refresh();
    $this->listing->refresh();

    expect($this->sellerTeam->budget)->toBe($sellerInitialBudget + 1_000_000);
    expect($this->buyerTeam->budget)->toBe($buyerInitialBudget - 1_000_000);
    expect($this->player->team_id)->toBe($this->buyerTeam->id);
    expect($this->player->market_value)->toBeGreaterThan($oldValue);
    expect($this->listing->status)->toBe(TransferListingStatus::Sold);
    expect(Transaction::count())->toBe(1);
});

it('forbids buying your own listed player', function (): void {
    $response = $this->withHeaders(authHeaders($this->seller))
        ->postJson('/api/transfer-list/'.$this->listing->id.'/buy');

    $response->assertUnprocessable();
});

it('rejects buy when listing does not exist', function (): void {
    $response = $this->withHeaders(authHeaders($this->buyer))
        ->postJson('/api/transfer-list/9999999/buy');

    $response->assertUnprocessable();
});

it('rejects buy when listing is no longer active', function (): void {
    $this->listing->update(['status' => TransferListingStatus::Cancelled]);

    $response = $this->withHeaders(authHeaders($this->buyer))
        ->postJson('/api/transfer-list/'.$this->listing->id.'/buy');

    $response->assertUnprocessable();
});

it('rejects buy when buyer has insufficient budget', function (): void {
    $this->buyerTeam->update(['budget' => 100]);

    $response = $this->withHeaders(authHeaders($this->buyer))
        ->postJson('/api/transfer-list/'.$this->listing->id.'/buy');

    $response->assertUnprocessable();
});
