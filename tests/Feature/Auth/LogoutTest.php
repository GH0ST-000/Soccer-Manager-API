<?php

declare(strict_types=1);

use App\Models\User;

it('logs out an authenticated user', function (): void {
    $user = User::factory()->create();

    $response = $this->withHeaders(authHeaders($user))
        ->postJson('/api/logout');

    $response->assertOk()
        ->assertJsonStructure(['message']);
});

it('rejects logout when unauthenticated', function (): void {
    $response = $this->withHeaders(jsonHeaders())
        ->postJson('/api/logout');

    $response->assertUnauthorized();
});
