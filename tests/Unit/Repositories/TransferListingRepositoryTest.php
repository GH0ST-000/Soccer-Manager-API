<?php

declare(strict_types=1);

use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\Team;
use App\Models\TransferListing;
use App\Repositories\TransferListingRepository;

it('creates, finds, locks, paginates, marks sold and cancelled', function (): void {
    $repo = new TransferListingRepository;
    $team = Team::factory()->create();
    $player = Player::factory()->create(['team_id' => $team->id]);

    $listing = $repo->create([
        'player_id' => $player->id,
        'seller_team_id' => $team->id,
        'asking_price' => 1_000_000,
    ]);

    expect($listing->status)->toBe(TransferListingStatus::Active)
        ->and($repo->findActiveForPlayer($player->id)?->id)->toBe($listing->id)
        ->and($repo->findActiveForPlayer(99999))->toBeNull()
        ->and($repo->lockById($listing->id)?->id)->toBe($listing->id)
        ->and($repo->lockById(99999))->toBeNull();

    $page = $repo->paginateActive([], 5);
    expect($page->total())->toBe(1)
        ->and($repo->markCancelled($listing)->status)->toBe(TransferListingStatus::Cancelled);

    $listing2 = TransferListing::factory()->create([
        'player_id' => $player->id,
        'seller_team_id' => $team->id,
    ]);
    expect($repo->markSold($listing2)->status)->toBe(TransferListingStatus::Sold);
});

it('paginates with all filters applied', function (): void {
    $repo = new TransferListingRepository;
    $team = Team::factory()->create(['name' => 'Spartak']);
    $player = Player::factory()->create([
        'team_id' => $team->id,
        'first_name' => 'Lionel',
        'last_name' => 'Messi',
        'country' => 'Argentina',
    ]);

    $repo->create([
        'player_id' => $player->id,
        'seller_team_id' => $team->id,
        'asking_price' => 2_000_000,
    ]);

    $page = $repo->paginateActive([
        'team_name' => 'Spartak',
        'player_name' => 'Lionel',
        'country' => 'Argentina',
        'min_price' => 1_000_000,
        'max_price' => 5_000_000,
    ], 10);

    expect($page->total())->toBe(1);

    $emptyPage = $repo->paginateActive([
        'team_name' => 'NonExistent',
    ], 10);

    expect($emptyPage->total())->toBe(0);
});
