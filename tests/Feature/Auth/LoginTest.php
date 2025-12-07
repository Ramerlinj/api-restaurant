<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('issues a Sanctum token for valid credentials', function (): void {
    $password = 'Secret123!';
    $user = User::factory()->create([
        'password' => Hash::make($password),
    ]);

    $response = postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => $password,
        'device_name' => 'pest-suite',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email'],
                'access_token',
                'token_type',
            ],
        ]);

    expect($user->tokens)->toHaveCount(1);
});

it('registers a new user and issues a token', function (): void {
    $response = postJson('/api/auth/register', [
        'name' => 'New User',
        'email' => 'new.user@example.com',
        'password' => 'Secret123!',
        'password_confirmation' => 'Secret123!',
        'device_name' => 'pest-suite',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.user.email', 'new.user@example.com')
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email'],
                'access_token',
                'token_type',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'new.user@example.com',
    ]);
});

it('validates duplicated emails on registration', function (): void {
    User::factory()->create([
        'email' => 'duplicate@example.com',
    ]);

    $response = postJson('/api/auth/register', [
        'name' => 'Dup User',
        'email' => 'duplicate@example.com',
        'password' => 'Secret123!',
        'password_confirmation' => 'Secret123!',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects invalid credentials', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('CorrectHorse1!'),
    ]);

    $response = postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('returns the authenticated user and revokes tokens on logout', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test-device');
    $plainTextToken = $token->plainTextToken;
    $tokenId = $token->accessToken->id;

    $meResponse = getJson('/api/auth/me', [
        'Authorization' => 'Bearer ' . $plainTextToken,
    ]);

    $meResponse->assertOk()
        ->assertJsonPath('data.user.email', $user->email);

    $logoutResponse = postJson('/api/auth/logout', [], [
        'Authorization' => 'Bearer ' . $plainTextToken,
    ]);

    $logoutResponse->assertNoContent();

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $tokenId,
    ]);
});

it('updates the authenticated user profile', function (): void {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'phone' => '12345',
    ]);

    $token = $user->createToken('test-device');

    $response = putJson('/api/auth/me', [
        'name' => 'New Name',
        'email' => 'new@example.com',
        'phone' => '98765',
    ], [
        'Authorization' => 'Bearer ' . $token->plainTextToken,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.user.name', 'New Name')
        ->assertJsonPath('data.user.email', 'new@example.com');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
        'email' => 'new@example.com',
        'phone' => '98765',
    ]);
});

it('deletes the authenticated user', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test-device');

    $response = deleteJson('/api/auth/me', [], [
        'Authorization' => 'Bearer ' . $token->plainTextToken,
    ]);

    $response->assertNoContent();

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});
