<?php

declare(strict_types=1);

use App\Models\Player;
use App\Models\Team;
use App\Models\User;

it('registers a user, creates a team and 20 players, and returns a JWT token', function (): void {
    $response = $this->postJson('/api/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email'],
            'token',
            'token_type',
        ]);

    expect(User::count())->toBe(1)
        ->and(Team::count())->toBe(1)
        ->and(Player::count())->toBe(20);

    $team = Team::first();
    expect($team)->not->toBeNull()
        ->and($team->budget)->toBe(5_000_000);

    $positions = Player::query()->select('position')->get()->groupBy('position');
    expect($positions->get('goalkeeper'))->toHaveCount(3)
        ->and($positions->get('defender'))->toHaveCount(6)
        ->and($positions->get('midfielder'))->toHaveCount(6)
        ->and($positions->get('attacker'))->toHaveCount(5);

    foreach (Player::all() as $player) {
        expect($player->market_value)->toBe(1_000_000)
            ->and($player->age)->toBeGreaterThanOrEqual(18)->toBeLessThanOrEqual(40);
    }
});

it('validates required fields on register', function (): void {
    $response = $this->postJson('/api/register', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('rejects duplicate email', function (): void {
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Test',
        'email' => 'taken@example.com',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('requires confirmed password', function (): void {
    $response = $this->postJson('/api/register', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'secret-password',
        'password_confirmation' => 'wrong-confirmation',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});
