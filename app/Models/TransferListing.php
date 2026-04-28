<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransferListingStatus;
use Database\Factories\TransferListingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $player_id
 * @property int $seller_team_id
 * @property int $asking_price
 * @property TransferListingStatus $status
 * @property Player $player
 * @property Team $sellerTeam
 */
#[Fillable([
    'player_id',
    'seller_team_id',
    'asking_price',
    'status',
])]
class TransferListing extends Model
{
    /** @use HasFactory<TransferListingFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Player, $this>
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function sellerTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'seller_team_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TransferListingStatus::class,
            'asking_price' => 'integer',
        ];
    }
}
