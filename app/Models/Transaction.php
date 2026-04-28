<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $player_id
 * @property int $seller_team_id
 * @property int $buyer_team_id
 * @property int $price
 * @property int $old_value
 * @property int $new_value
 */
#[Fillable([
    'player_id',
    'seller_team_id',
    'buyer_team_id',
    'price',
    'old_value',
    'new_value',
])]
class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
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
     * @return BelongsTo<Team, $this>
     */
    public function buyerTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'buyer_team_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'old_value' => 'integer',
            'new_value' => 'integer',
        ];
    }
}
