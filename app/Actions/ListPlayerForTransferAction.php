<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Player;
use App\Models\TransferListing;
use App\Repositories\Contracts\TransferListingRepositoryInterface;
use Illuminate\Validation\ValidationException;

final readonly class ListPlayerForTransferAction
{
    public function __construct(
        private TransferListingRepositoryInterface $listings,
    ) {}

    public function execute(Player $player, int $askingPrice): TransferListing
    {
        if ($this->listings->findActiveForPlayer($player->id) instanceof TransferListing) {
            throw ValidationException::withMessages([
                'player_id' => __('soccer.transfer.already_listed'),
            ]);
        }

        return $this->listings->create([
            'player_id' => $player->id,
            'seller_team_id' => $player->team_id,
            'asking_price' => $askingPrice,
        ]);
    }
}
