<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\BuyPlayerAction;
use App\Actions\CancelTransferListingAction;
use App\Actions\ListPlayerForTransferAction;
use App\Models\Player;
use App\Models\Team;
use App\Models\Transaction;
use App\Models\TransferListing;
use App\Repositories\Contracts\TransferListingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class TransferService
{
    public function __construct(
        private TransferListingRepositoryInterface $listings,
        private ListPlayerForTransferAction $listPlayer,
        private CancelTransferListingAction $cancelListing,
        private BuyPlayerAction $buyPlayer,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, TransferListing>
     */
    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->listings->paginateActive($filters, $perPage);
    }

    public function listPlayer(Player $player, int $askingPrice): TransferListing
    {
        return $this->listPlayer->execute($player, $askingPrice);
    }

    public function cancelListing(Player $player): TransferListing
    {
        return $this->cancelListing->execute($player);
    }

    public function buy(int $listingId, Team $buyerTeam): Transaction
    {
        return $this->buyPlayer->execute($listingId, $buyerTeam);
    }
}
