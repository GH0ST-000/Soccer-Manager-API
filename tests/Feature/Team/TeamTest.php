<?php

declare(strict_types=1);

use App\Actions\CreateInitialTeamAction;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    app(CreateInitialTeamAction::class)->execute($this->user);
    $this->user->refresh();
});

it('returns the authenticated user team with players', function (): void {
    $response = $this->withHeaders(authHeaders($this->user))
        ->getJson('/api/team');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id', 'user_id', 'name', 'country', 'budget',
                'players' => [
                    '*' => ['id', 'first_name', 'last_name', 'country', 'position', 'age', 'market_value'],
                ],
            ],
        ]);

    expect($response->json('data.players'))->toHaveCount(20);
});

it('updates the authenticated user team', function (): void {
    $response = $this->withHeaders(authHeaders($this->user))
        ->putJson('/api/team', [
            'name' => 'Renamed FC',
            'country' => 'Atlantis',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Renamed FC')
        ->assertJsonPath('data.country', 'Atlantis');
});

it('validates team update fields when provided', function (): void {
    $response = $this->withHeaders(authHeaders($this->user))
        ->putJson('/api/team', [
            'name' => '',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('requires authentication for team endpoints', function (): void {
    $this->getJson('/api/team', jsonHeaders())->assertUnauthorized();
    $this->putJson('/api/team', ['name' => 'X'], jsonHeaders())->assertUnauthorized();
});

it('returns 404 if user has no team', function (): void {
    $orphan = User::factory()->create();

    $response = $this->withHeaders(authHeaders($orphan))
        ->getJson('/api/team');

    $response->assertNotFound();
});
