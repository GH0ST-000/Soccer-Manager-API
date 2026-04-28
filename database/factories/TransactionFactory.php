<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Player;
use App\Models\Team;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'player_id' => Player::factory(),
            'seller_team_id' => Team::factory(),
            'buyer_team_id' => Team::factory(),
            'price' => 1_000_000,
            'old_value' => 1_000_000,
            'new_value' => 1_500_000,
        ];
    }
}
