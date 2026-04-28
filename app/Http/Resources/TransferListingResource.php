<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\TransferListing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TransferListing
 */
final class TransferListingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'player_id' => $this->player_id,
            'seller_team_id' => $this->seller_team_id,
            'asking_price' => $this->asking_price,
            'status' => $this->status->value,
            'player' => new PlayerResource($this->whenLoaded('player')),
            'seller_team' => new TeamResource($this->whenLoaded('sellerTeam')),
        ];
    }
}
