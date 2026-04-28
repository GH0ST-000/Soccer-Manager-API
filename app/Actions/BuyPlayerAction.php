<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\Team;
use App\Models\Transaction;
use App\Models\TransferListing;
use App\Repositories\Contracts\PlayerRepositoryInterface;
use App\Repositories\Contracts\TeamRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\TransferListingRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class BuyPlayerAction
{
    /**
     * Lower bound (inclusive) of the random multiplier applied to a player's
     * market value after they are sold. Expressed in percentage points.
     */
    public const int MIN_VALUE_INCREASE_PERCENT = 10;

    public const int MAX_VALUE_INCREASE_PERCENT = 100;

    public function __construct(
        private TransferListingRepositoryInterface $listings,
        private PlayerRepositoryInterface $players,
        private TeamRepositoryInterface $teams,
        private TransactionRepositoryInterface $transactions,
    ) {}

    public function execute(int $listingId, Team $buyerTeam): Transaction
    {
        /** @var Transaction $transaction */
        $transaction = DB::transaction(function () use ($listingId, $buyerTeam): Transaction {
            $listing = $this->listings->lockById($listingId);

            if (! $listing instanceof TransferListing || $listing->status !== TransferListingStatus::Active) {
                throw ValidationException::withMessages([
                    'listing_id' => __('soccer.transfer.listing_not_active'),
                ]);
            }

            if ($listing->seller_team_id === $buyerTeam->id) {
                throw ValidationException::withMessages([
                    'listing_id' => __('soccer.transfer.cannot_buy_own_player'),
                ]);
            }

            $sellerTeam = $this->teams->lockById($listing->seller_team_id);
            $lockedBuyer = $this->teams->lockById($buyerTeam->id);
            $player = $this->players->lockById($listing->player_id);

            if (! $sellerTeam instanceof Team || ! $lockedBuyer instanceof Team || ! $player instanceof Player) {
                throw ValidationException::withMessages([
                    'listing_id' => __('soccer.transfer.listing_not_active'),
                ]);
            }

            $price = $listing->asking_price;

            if ($lockedBuyer->budget < $price) {
                throw ValidationException::withMessages([
                    'budget' => __('soccer.transfer.insufficient_budget'),
                ]);
            }

            $oldValue = $player->market_value;
            $newValue = $this->calculateNewValue($oldValue);

            $this->teams->adjustBudget($lockedBuyer, -$price);
            $this->teams->adjustBudget($sellerTeam, $price);

            $this->players->update($player, [
                'team_id' => $lockedBuyer->id,
                'market_value' => $newValue,
            ]);

            $this->listings->markSold($listing);

            return $this->transactions->create([
                'player_id' => $player->id,
                'seller_team_id' => $sellerTeam->id,
                'buyer_team_id' => $lockedBuyer->id,
                'price' => $price,
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ]);
        });

        return $transaction;
    }

    private function calculateNewValue(int $oldValue): int
    {
        $percent = random_int(self::MIN_VALUE_INCREASE_PERCENT, self::MAX_VALUE_INCREASE_PERCENT);

        return (int) round($oldValue + ($oldValue * $percent / 100));
    }
}
