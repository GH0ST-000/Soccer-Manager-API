<?php

declare(strict_types=1);

use App\Actions\CreateInitialTeamAction;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    app(CreateInitialTeamAction::class)->execute($this->user);
    $this->user->refresh();
    $this->team = $this->user->team;
});

it('lists players for authenticated user team', function (): void {
    $response = $this->withHeaders(authHeaders($this->user))
        ->getJson('/api/players');

    $response->assertOk()
        ->assertJsonCount(20, 'data');
});

it('updates a player owned by the user', function (): void {
    /** @var Player $player */
    $player = $this->team->players()->first();

    $response = $this->withHeaders(authHeaders($this->user))
        ->putJson('/api/players/'.$player->id, [
            'first_name' => 'Lionel',
            'last_name' => 'Messi',
            'country' => 'Argentina',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.first_name', 'Lionel')
        ->assertJsonPath('data.last_name', 'Messi')
        ->assertJsonPath('data.country', 'Argentina');
});

it('forbids updating a player owned by another team', function (): void {
    $other = User::factory()->create();
    app(CreateInitialTeamAction::class)->execute($other);
    $other->refresh();
    /** @var Team $otherTeam */
    $otherTeam = $other->team;
    /** @var Player $foreignPlayer */
    $foreignPlayer = $otherTeam->players()->first();

    $response = $this->withHeaders(authHeaders($this->user))
        ->putJson('/api/players/'.$foreignPlayer->id, ['first_name' => 'Hacked']);

    $response->assertForbidden();
});

it('returns 404 for a non-existent player', function (): void {
    $response = $this->withHeaders(authHeaders($this->user))
        ->putJson('/api/players/999999', ['first_name' => 'Ghost']);

    $response->assertNotFound();
});

it('validates player update payload', function (): void {
    /** @var Player $player */
    $player = $this->team->players()->first();

    $response = $this->withHeaders(authHeaders($this->user))
        ->putJson('/api/players/'.$player->id, [
            'first_name' => '',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['first_name']);
});

it('requires authentication for player endpoints', function (): void {
    $this->getJson('/api/players', jsonHeaders())->assertUnauthorized();
    $this->putJson('/api/players/1', [], jsonHeaders())->assertUnauthorized();
});
