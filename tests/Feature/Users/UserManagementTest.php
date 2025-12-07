<?php

use App\Models\User;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\putJson;

it('prevents non admins from updating other users', function (): void {
    $actor = User::factory()->create();
    $target = User::factory()->create();

    $token = $actor->createToken('test-device')->plainTextToken;

    $response = putJson('/api/users/' . $target->id, [
        'name' => 'Unauthorized',
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertForbidden();
});

it('allows admins to promote a user', function (): void {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $target = User::factory()->create([
        'role' => User::ROLE_USER,
    ]);

    $token = $admin->createToken('test-device')->plainTextToken;

    $response = putJson('/api/users/' . $target->id, [
        'role' => User::ROLE_ADMIN,
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.user.role', User::ROLE_ADMIN);
});

it('prevents admins from modifying other admins', function (): void {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $target = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $token = $admin->createToken('test-device')->plainTextToken;

    $response = putJson('/api/users/' . $target->id, [
        'name' => 'New Name',
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertForbidden();
});

it('prevents admins from modifying superadmins', function (): void {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $target = User::factory()->create([
        'role' => User::ROLE_SUPERADMIN,
    ]);

    $token = $admin->createToken('test-device')->plainTextToken;

    $response = putJson('/api/users/' . $target->id, [
        'name' => 'Should Fail',
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertForbidden();
});

it('allows superadmins to modify admins and delete them', function (): void {
    $super = User::factory()->create([
        'role' => User::ROLE_SUPERADMIN,
    ]);

    $target = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $token = $super->createToken('test-device')->plainTextToken;

    $updateResponse = putJson('/api/users/' . $target->id, [
        'name' => 'Admin Updated',
        'surname' => 'Admin Family',
        'role' => User::ROLE_USER,
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $updateResponse->assertOk()
        ->assertJsonPath('data.user.name', 'Admin Updated')
        ->assertJsonPath('data.user.surname', 'Admin Family')
        ->assertJsonPath('data.user.role', User::ROLE_USER);

    $deleteResponse = deleteJson('/api/users/' . $target->id, [], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $deleteResponse->assertNoContent();
});

it('allows superadmins to update other superadmins', function (): void {
    $super = User::factory()->create([
        'role' => User::ROLE_SUPERADMIN,
        'name' => 'Root One',
    ]);

    $target = User::factory()->create([
        'role' => User::ROLE_SUPERADMIN,
        'name' => 'Root Two',
    ]);

    $token = $super->createToken('test-device')->plainTextToken;

    $response = putJson('/api/users/' . $target->id, [
        'name' => 'Root Updated',
        'surname' => 'Root Family',
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.user.name', 'Root Updated')
        ->assertJsonPath('data.user.surname', 'Root Family');
});