<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\TransferListing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TransferListingRepositoryInterface
{
    /**
     * @param  array{player_id: int, seller_team_id: int, asking_price: int}  $attributes
     */
    public function create(array $attributes): TransferListing;

    public function findActiveForPlayer(int $playerId): ?TransferListing;

    public function lockById(int $listingId): ?TransferListing;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, TransferListing>
     */
    public function paginateActive(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function markCancelled(TransferListing $listing): TransferListing;

    public function markSold(TransferListing $listing): TransferListing;
}
