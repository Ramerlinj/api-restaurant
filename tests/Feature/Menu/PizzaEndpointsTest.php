<?php

use App\Models\User;
use App\Modules\Menu\Models\Pizza;
use App\Services\CloudinaryService;
use Laravel\Sanctum\Sanctum;
use Mockery as MockeryFacade;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('stores a pizza with an uploaded image', function (): void {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $cloudinaryPayload = [
        'public_id' => 'api-restaurant/pizzas/diavola',
        'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v123/diavola.png',
        'resource_type' => 'image',
        'bytes' => 23456,
        'width' => 1200,
        'height' => 900,
        'format' => 'png',
    ];

    $mock = MockeryFacade::mock(CloudinaryService::class);
    $mock->shouldReceive('uploadImage')
        ->once()
        ->andReturnUsing(fn() => $cloudinaryPayload);

    app()->instance(CloudinaryService::class, $mock);

    Sanctum::actingAs($admin);

    $response = postJson('/api/pizzas', [
        'name' => 'Diavola',
        'description' => 'Spicy salami with fresh mozzarella.',
        'price' => 12.5,
        'is_recommended' => true,
        'image' => fakePngUpload('diavola.png'),
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.pizza.name', 'Diavola')
        ->assertJsonPath('data.pizza.is_recommended', true)
        ->assertJsonPath('data.pizza.image_url.public_id', 'api-restaurant/pizzas/diavola');

    $this->assertDatabaseHas('pizzas', [
        'name' => 'Diavola',
        'is_recommended' => true,
    ]);
});

it('blocks non admins from creating pizzas', function (): void {
    $user = User::factory()->create([
        'role' => User::ROLE_USER,
    ]);

    Sanctum::actingAs($user);

    $response = postJson('/api/pizzas', [
        'name' => 'Margherita',
        'price' => 9.5,
        'image' => fakePngUpload('margherita.png'),
    ]);

    $response->assertForbidden();
});

it('lists pizzas for the catalog', function (): void {
    $older = Pizza::query()->create([
        'name' => 'Margherita',
        'description' => 'Tomato, mozzarella, basil.',
        'price' => 9.5,
        'is_recommended' => true,
    ]);
    Pizza::query()->whereKey($older->id)->update(['created_at' => now()->subDay()]);

    Pizza::query()->create([
        'name' => 'BBQ Chicken',
        'description' => 'BBQ sauce with chicken and onions.',
        'price' => 13.75,
        'is_recommended' => false,
    ]);

    $response = getJson('/api/pizzas');

    $response->assertOk()
        ->assertJsonCount(2, 'data.pizzas')
        ->assertJsonPath('data.pizzas.0.name', 'BBQ Chicken')
        ->assertJsonPath('data.pizzas.1.name', 'Margherita');
});

it('updates a pizza and refreshes the image asset', function (): void {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $pizza = Pizza::query()->create([
        'name' => 'Hawaiian',
        'description' => 'Pineapple and ham.',
        'price' => 11.25,
        'is_recommended' => false,
        'image_url' => [
            'public_id' => 'api-restaurant/pizzas/hawaiian-old',
        ],
    ]);

    $cloudinaryPayload = [
        'public_id' => 'api-restaurant/pizzas/hawaiian',
        'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v124/hawaiian.png',
        'resource_type' => 'image',
        'bytes' => 34567,
        'width' => 1100,
        'height' => 1100,
        'format' => 'png',
    ];

    $mock = MockeryFacade::mock(CloudinaryService::class);
    $mock->shouldReceive('uploadImage')
        ->once()
        ->andReturnUsing(fn() => $cloudinaryPayload);

    app()->instance(CloudinaryService::class, $mock);

    Sanctum::actingAs($admin);

    $response = putJson("/api/pizzas/{$pizza->id}", [
        'name' => 'Hawaiian Deluxe',
        'price' => 12.25,
        'is_recommended' => true,
        'image' => fakePngUpload('hawaiian.png'),
    ]);

    $response->assertOk()
        ->assertJsonPath('data.pizza.name', 'Hawaiian Deluxe')
        ->assertJsonPath('data.pizza.image_url.public_id', 'api-restaurant/pizzas/hawaiian');

    $this->assertDatabaseHas('pizzas', [
        'id' => $pizza->id,
        'name' => 'Hawaiian Deluxe',
        'is_recommended' => true,
    ]);
});

it('prevents non admins from updating pizzas', function (): void {
    $user = User::factory()->create([
        'role' => User::ROLE_USER,
    ]);

    $pizza = Pizza::query()->create([
        'name' => 'Veggie Lovers',
        'price' => 10.75,
    ]);

    Sanctum::actingAs($user);

    $response = putJson("/api/pizzas/{$pizza->id}", [
        'name' => 'Ultimate Veggie',
    ]);

    $response->assertForbidden();
});
