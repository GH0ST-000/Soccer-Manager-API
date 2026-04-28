<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\Team;
use App\Models\TransferListing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferListing>
 */
class TransferListingFactory extends Factory
{
    protected $model = TransferListing::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'player_id' => Player::factory(),
            'seller_team_id' => Team::factory(),
            'asking_price' => 1_000_000,
            'status' => TransferListingStatus::Active,
        ];
    }

    public function active(): self
    {
        return $this->state(fn (): array => ['status' => TransferListingStatus::Active]);
    }

    public function sold(): self
    {
        return $this->state(fn (): array => ['status' => TransferListingStatus::Sold]);
    }

    public function cancelled(): self
    {
        return $this->state(fn (): array => ['status' => TransferListingStatus::Cancelled]);
    }
}
