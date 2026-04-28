<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Transaction
 */
final class TransactionResource extends JsonResource
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
            'buyer_team_id' => $this->buyer_team_id,
            'price' => $this->price,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
        ];
    }
}
