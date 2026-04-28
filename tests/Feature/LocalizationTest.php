<?php

declare(strict_types=1);

use App\Models\User;

it('returns Georgian messages when Accept-Language is ka', function (): void {
    $response = $this->postJson('/api/login', [
        'email' => 'nope@example.com',
        'password' => 'whatever',
    ], ['Accept-Language' => 'ka']);

    $response->assertUnauthorized()
        ->assertJsonPath('message', __('soccer.auth.invalid_credentials', [], 'ka'));
});

it('falls back to English for unsupported locales', function (): void {
    $response = $this->postJson('/api/login', [
        'email' => 'nope@example.com',
        'password' => 'whatever',
    ], ['Accept-Language' => 'fr']);

    $response->assertUnauthorized()
        ->assertJsonPath('message', __('soccer.auth.invalid_credentials', [], 'en'));
});

it('returns Georgian message on unauthenticated requests', function (): void {
    $response = $this->getJson('/api/team', ['Accept-Language' => 'ka']);

    $response->assertUnauthorized()
        ->assertJsonPath('message', __('soccer.auth.unauthenticated', [], 'ka'));
});

it('localizes team-not-found errors when an authenticated user lacks a team', function (): void {
    $user = User::factory()->create();

    $response = $this->withHeaders([
        ...authHeaders($user),
        'Accept-Language' => 'ka',
    ])->getJson('/api/team');

    $response->assertNotFound();
});

it('localizes framework validation messages to Georgian', function (): void {
    $response = $this->postJson('/api/register', [], [
        'Accept' => 'application/json',
        'Accept-Language' => 'ka',
    ]);

    $response->assertUnprocessable();

    $errors = $response->json('errors');
    expect($errors['name'][0])->toBe(__('validation.required', ['attribute' => __('validation.attributes.name', [], 'ka')], 'ka'));
    expect($errors['email'][0])->toBe(__('validation.required', ['attribute' => __('validation.attributes.email', [], 'ka')], 'ka'));
    expect($errors['password'][0])->toBe(__('validation.required', ['attribute' => __('validation.attributes.password', [], 'ka')], 'ka'));

    expect($errors['name'][0])->not->toContain('field is required');
});
