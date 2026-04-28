<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlayerPosition;
use App\Enums\TransferListingStatus;
use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $team_id
 * @property string $first_name
 * @property string $last_name
 * @property string $country
 * @property PlayerPosition $position
 * @property int $age
 * @property int $market_value
 * @property Team $team
 * @property TransferListing|null $activeListing
 */
#[Fillable([
    'team_id',
    'first_name',
    'last_name',
    'country',
    'position',
    'age',
    'market_value',
])]
class Player extends Model
{
    /** @use HasFactory<PlayerFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return HasOne<TransferListing, $this>
     */
    public function activeListing(): HasOne
    {
        /** @var HasOne<TransferListing, $this> $relation */
        $relation = $this->hasOne(TransferListing::class)
            ->where('status', TransferListingStatus::Active->value);

        return $relation;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => PlayerPosition::class,
            'age' => 'integer',
            'market_value' => 'integer',
        ];
    }
}
