<?php

declare(strict_types=1);

use App\Actions\BuyPlayerAction;
use App\Actions\ListPlayerForTransferAction;
use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\Team;
use App\Models\Transaction;
use App\Repositories\Contracts\PlayerRepositoryInterface;
use App\Repositories\Contracts\TeamRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\TransferListingRepositoryInterface;
use Illuminate\Validation\ValidationException;

beforeEach(function (): void {
    $this->seller = Team::factory()->create(['budget' => 5_000_000]);
    $this->buyer = Team::factory()->create(['budget' => 5_000_000]);
    $this->player = Player::factory()->create([
        'team_id' => $this->seller->id,
        'market_value' => 1_000_000,
    ]);

    $this->listing = app(ListPlayerForTransferAction::class)->execute($this->player, 1_000_000);
});

it('completes a purchase, transferring ownership and adjusting budgets', function (): void {
    $transaction = app(BuyPlayerAction::class)->execute($this->listing->id, $this->buyer);

    expect($transaction)->toBeInstanceOf(Transaction::class);

    $this->seller->refresh();
    $this->buyer->refresh();
    $this->player->refresh();
    $this->listing->refresh();

    expect($this->seller->budget)->toBe(6_000_000)
        ->and($this->buyer->budget)->toBe(4_000_000)
        ->and($this->player->team_id)->toBe($this->buyer->id)
        ->and($this->listing->status)->toBe(TransferListingStatus::Sold)
        ->and($transaction->old_value)->toBe(1_000_000)
        ->and($transaction->new_value)->toBeGreaterThan(1_000_000);
});

it('throws when the listing does not exist', function (): void {
    app(BuyPlayerAction::class)->execute(999999, $this->buyer);
})->throws(ValidationException::class);

it('throws when the buyer is the seller', function (): void {
    app(BuyPlayerAction::class)->execute($this->listing->id, $this->seller);
})->throws(ValidationException::class);

it('throws when the buyer cannot afford the player', function (): void {
    $this->buyer->update(['budget' => 1]);

    app(BuyPlayerAction::class)->execute($this->listing->id, $this->buyer);
})->throws(ValidationException::class);

it('throws when the listing is not active anymore', function (): void {
    $this->listing->update(['status' => TransferListingStatus::Cancelled]);

    app(BuyPlayerAction::class)->execute($this->listing->id, $this->buyer);
})->throws(ValidationException::class);

it('throws when locked dependencies are missing (defensive branch)', function (): void {
    $listingRepo = Mockery::mock(TransferListingRepositoryInterface::class);
    $listingRepo->shouldReceive('lockById')
        ->andReturn($this->listing);

    $teamRepo = Mockery::mock(TeamRepositoryInterface::class);
    $teamRepo->shouldReceive('lockById')->andReturn(null);

    $playerRepo = Mockery::mock(PlayerRepositoryInterface::class);
    $playerRepo->shouldReceive('lockById')->andReturn(null);

    $txRepo = Mockery::mock(TransactionRepositoryInterface::class);

    $action = new BuyPlayerAction($listingRepo, $playerRepo, $teamRepo, $txRepo);

    expect(fn (): Transaction => $action->execute($this->listing->id, $this->buyer))
        ->toThrow(ValidationException::class);
});

it('uses the value increase formula within bounds', function (): void {
    $reflection = new ReflectionMethod(BuyPlayerAction::class, 'calculateNewValue');

    $action = app(BuyPlayerAction::class);

    for ($i = 0; $i < 20; $i++) {
        $value = $reflection->invoke($action, 1_000_000);
        expect($value)->toBeGreaterThanOrEqual(1_100_000)
            ->and($value)->toBeLessThanOrEqual(2_000_000);
    }
});
