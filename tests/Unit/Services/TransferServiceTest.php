<?php

declare(strict_types=1);

use App\Actions\CreateInitialTeamAction;
use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Services\TransferService;

it('lists, cancels, searches, and buys players via the service facade', function (): void {
    $sellerOwner = User::factory()->create();
    app(CreateInitialTeamAction::class)->execute($sellerOwner);
    $sellerOwner->refresh();
    /** @var Team $sellerTeam */
    $sellerTeam = $sellerOwner->team;
    /** @var Player $player */
    $player = $sellerTeam->players()->first();

    $buyerOwner = User::factory()->create();
    app(CreateInitialTeamAction::class)->execute($buyerOwner);
    $buyerOwner->refresh();
    /** @var Team $buyerTeam */
    $buyerTeam = $buyerOwner->team;

    /** @var TransferService $service */
    $service = app(TransferService::class);

    $listing = $service->listPlayer($player, 1_000_000);
    expect($listing->status)->toBe(TransferListingStatus::Active);

    $page = $service->search([], 5);
    expect($page->total())->toBe(1);

    $tx = $service->buy($listing->id, $buyerTeam);
    expect($tx->buyer_team_id)->toBe($buyerTeam->id);

    $newListing = $service->listPlayer($player->fresh(), 2_000_000);
    $cancelled = $service->cancelListing($player->fresh());
    expect($cancelled->id)->toBe($newListing->id);
    expect($cancelled->status)->toBe(TransferListingStatus::Cancelled);
});
