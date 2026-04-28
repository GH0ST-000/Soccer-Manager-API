<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\TransferListingStatus;
use App\Models\TransferListing;
use App\Repositories\Contracts\TransferListingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class TransferListingRepository implements TransferListingRepositoryInterface
{
    public function create(array $attributes): TransferListing
    {
        return TransferListing::query()->create([
            ...$attributes,
            'status' => TransferListingStatus::Active,
        ]);
    }

    public function findActiveForPlayer(int $playerId): ?TransferListing
    {
        return TransferListing::query()
            ->where('player_id', $playerId)
            ->where('status', TransferListingStatus::Active->value)
            ->first();
    }

    public function lockById(int $listingId): ?TransferListing
    {
        return TransferListing::query()->where('id', $listingId)->lockForUpdate()->first();
    }

    public function paginateActive(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $teamName = isset($filters['team_name']) && is_string($filters['team_name']) ? $filters['team_name'] : null;
        $country = isset($filters['country']) && is_string($filters['country']) ? $filters['country'] : null;
        $playerName = isset($filters['player_name']) && is_string($filters['player_name']) ? $filters['player_name'] : null;
        $minPrice = isset($filters['min_price']) && is_numeric($filters['min_price']) ? (int) $filters['min_price'] : null;
        $maxPrice = isset($filters['max_price']) && is_numeric($filters['max_price']) ? (int) $filters['max_price'] : null;

        return TransferListing::query()
            ->with(['player', 'sellerTeam'])
            ->where('status', TransferListingStatus::Active->value)
            ->when(
                $teamName !== null,
                fn (Builder $q): Builder => $q->whereHas(
                    'sellerTeam',
                    fn (Builder $teamQuery): Builder => $teamQuery->where('name', 'like', '%'.$teamName.'%')
                ),
            )
            ->when(
                $country !== null,
                fn (Builder $q): Builder => $q->whereHas(
                    'player',
                    fn (Builder $pq): Builder => $pq->where('country', 'like', '%'.$country.'%')
                ),
            )
            ->when(
                $playerName !== null,
                fn (Builder $q): Builder => $q->whereHas(
                    'player',
                    fn (Builder $pq): Builder => $pq
                        ->where('first_name', 'like', '%'.$playerName.'%')
                        ->orWhere('last_name', 'like', '%'.$playerName.'%')
                ),
            )
            ->when(
                $minPrice !== null,
                fn (Builder $q): Builder => $q->where('asking_price', '>=', $minPrice),
            )
            ->when(
                $maxPrice !== null,
                fn (Builder $q): Builder => $q->where('asking_price', '<=', $maxPrice),
            )
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function markCancelled(TransferListing $listing): TransferListing
    {
        $listing->status = TransferListingStatus::Cancelled;
        $listing->save();

        return $listing;
    }

    public function markSold(TransferListing $listing): TransferListing
    {
        $listing->status = TransferListingStatus::Sold;
        $listing->save();

        return $listing;
    }
}
