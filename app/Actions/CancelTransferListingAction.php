<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Player;
use App\Models\TransferListing;
use App\Repositories\Contracts\TransferListingRepositoryInterface;
use Illuminate\Validation\ValidationException;

final readonly class CancelTransferListingAction
{
    public function __construct(
        private TransferListingRepositoryInterface $listings,
    ) {}

    public function execute(Player $player): TransferListing
    {
        $listing = $this->listings->findActiveForPlayer($player->id);

        if (! $listing instanceof TransferListing) {
            throw ValidationException::withMessages([
                'player_id' => __('soccer.transfer.not_listed'),
            ]);
        }

        return $this->listings->markCancelled($listing);
    }
}
