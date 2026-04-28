<?php

declare(strict_types=1);

use App\Actions\CreateInitialTeamAction;
use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\Team;
use App\Models\TransferListing;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    app(CreateInitialTeamAction::class)->execute($this->user);
    $this->user->refresh();
    /** @var Team $team */
    $team = $this->user->team;
    $this->team = $team;
});

it('lists a player on the transfer market', function (): void {
    /** @var Player $player */
    $player = $this->team->players()->first();

    $response = $this->withHeaders(authHeaders($this->user))
        ->postJson('/api/players/'.$player->id.'/transfer-list', [
            'asking_price' => 2_500_000,
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.asking_price', 2_500_000)
        ->assertJsonPath('data.status', TransferListingStatus::Active->value);

    expect(TransferListing::count())->toBe(1);
});

it('rejects listing the same player twice', function (): void {
    /** @var Player $player */
    $player = $this->team->players()->first();

    $this->withHeaders(authHeaders($this->user))
        ->postJson('/api/players/'.$player->id.'/transfer-list', ['asking_price' => 1_000_000])
        ->assertCreated();

    $response = $this->withHeaders(authHeaders($this->user))
        ->postJson('/api/players/'.$player->id.'/transfer-list', ['asking_price' => 2_000_000]);

    $response->assertUnprocessable();
});

it('forbids listing a player not owned by the user', function (): void {
    $other = User::factory()->create();
    app(CreateInitialTeamAction::class)->execute($other);
    $other->refresh();
    /** @var Player $foreignPlayer */
    $foreignPlayer = $other->team->players()->first();

    $response = $this->withHeaders(authHeaders($this->user))
        ->postJson('/api/players/'.$foreignPlayer->id.'/transfer-list', ['asking_price' => 1_000_000]);

    $response->assertForbidden();
});

it('cancels an active listing', function (): void {
    /** @var Player $player */
    $player = $this->team->players()->first();

    $this->withHeaders(authHeaders($this->user))
        ->postJson('/api/players/'.$player->id.'/transfer-list', ['asking_price' => 1_500_000]);

    $response = $this->withHeaders(authHeaders($this->user))
        ->deleteJson('/api/players/'.$player->id.'/transfer-list');

    $response->assertOk()
        ->assertJsonPath('data.status', TransferListingStatus::Cancelled->value);
});

it('cannot cancel a listing that does not exist', function (): void {
    /** @var Player $player */
    $player = $this->team->players()->first();

    $response = $this->withHeaders(authHeaders($this->user))
        ->deleteJson('/api/players/'.$player->id.'/transfer-list');

    $response->assertUnprocessable();
});

it('searches and filters the transfer market', function (): void {
    /** @var Player $player */
    $player = $this->team->players()->first();
    $player->update(['first_name' => 'Kylian', 'last_name' => 'Mbappé', 'country' => 'France']);

    $this->withHeaders(authHeaders($this->user))
        ->postJson('/api/players/'.$player->id.'/transfer-list', ['asking_price' => 3_000_000]);

    $response = $this->withHeaders(authHeaders($this->user))
        ->getJson('/api/transfer-list?team_name='.urlencode((string) $this->team->name)
            .'&player_name=Kylian&country=France&min_price=1000000&max_price=5000000&per_page=10');

    $response->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);

    expect($response->json('data'))->toHaveCount(1);
});

it('validates list player payload', function (): void {
    /** @var Player $player */
    $player = $this->team->players()->first();

    $response = $this->withHeaders(authHeaders($this->user))
        ->postJson('/api/players/'.$player->id.'/transfer-list', [
            'asking_price' => 0,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['asking_price']);
});
