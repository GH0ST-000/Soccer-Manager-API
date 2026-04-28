<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Transaction;

interface TransactionRepositoryInterface
{
    /**
     * @param  array{
     *     player_id: int,
     *     seller_team_id: int,
     *     buyer_team_id: int,
     *     price: int,
     *     old_value: int,
     *     new_value: int,
     * }  $attributes
     */
    public function create(array $attributes): Transaction;
}
