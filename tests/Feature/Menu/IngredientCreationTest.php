<?php

use App\Models\User;
use App\Modules\Menu\Models\Ingredient;
use App\Services\CloudinaryService;
use Laravel\Sanctum\Sanctum;
use Mockery as MockeryFacade;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('stores an ingredient with an uploaded image', function (): void {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $payload = [
        'public_id' => 'api-restaurant/ingredients/cheddar',
        'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v123/cheddar.png',
        'resource_type' => 'image',
        'bytes' => 12345,
        'width' => 1000,
        'height' => 800,
        'format' => 'png',
    ];

    $mock = MockeryFacade::mock(CloudinaryService::class);
    $mock->shouldReceive('uploadImage')
        ->once()
        ->andReturnUsing(fn() => $payload);

    app()->instance(CloudinaryService::class, $mock);

    Sanctum::actingAs($admin);

    $response = postJson('/api/ingredients', [
        'name' => 'Cheddar',
        'price' => 5.5,
        'ingredient' => 'cheese',
        'available' => true,
        'image' => fakePngUpload('cheddar.png'),
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.ingredient.name', 'Cheddar')
        ->assertJsonPath('data.ingredient.image_url.public_id', 'api-restaurant/ingredients/cheddar');

    $this->assertDatabaseHas('ingredients', [
        'name' => 'Cheddar',
        'ingredient' => 'cheese',
    ]);

    expect(Ingredient::firstWhere('name', 'Cheddar')?->image_url)
        ->not()->toBeNull();
});

it('blocks non admins from creating ingredients', function (): void {
    $user = User::factory()->create([
        'role' => User::ROLE_USER,
    ]);

    Sanctum::actingAs($user);

    $response = postJson('/api/ingredients', [
        'name' => 'Mozzarella',
        'price' => 4.5,
        'ingredient' => 'cheese',
        'image' => fakePngUpload('mozzarella.png'),
    ]);

    $response->assertForbidden();
});

it('lists the available ingredients', function (): void {
    Ingredient::query()->create([
        'name' => 'Anchovy',
        'price' => 3.25,
        'ingredient' => 'topping',
        'available' => true,
    ]);

    Ingredient::query()->create([
        'name' => 'Zucchini',
        'price' => 2.75,
        'ingredient' => 'vegetable',
        'available' => false,
    ]);

    $response = getJson('/api/ingredients');

    $response->assertOk()
        ->assertJsonCount(2, 'data.ingredients')
        ->assertJsonPath('data.ingredients.0.name', 'Anchovy')
        ->assertJsonPath('data.ingredients.1.name', 'Zucchini');
});

it('updates an ingredient and replaces the image', function (): void {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $ingredient = Ingredient::query()->create([
        'name' => 'Olives',
        'price' => 2.15,
        'ingredient' => 'vegetable',
        'available' => true,
        'image_url' => [
            'public_id' => 'api-restaurant/ingredients/olives-old',
            'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v123/olives-old.png',
        ],
    ]);

    $payload = [
        'public_id' => 'api-restaurant/ingredients/olives',
        'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v124/olives.png',
        'resource_type' => 'image',
        'bytes' => 56789,
        'width' => 600,
        'height' => 600,
        'format' => 'png',
    ];

    $mock = MockeryFacade::mock(CloudinaryService::class);
    $mock->shouldReceive('uploadImage')
        ->once()
        ->andReturnUsing(fn() => $payload);

    app()->instance(CloudinaryService::class, $mock);

    Sanctum::actingAs($admin);

    $response = putJson("/api/ingredients/{$ingredient->id}", [
        'name' => 'Black Olives',
        'price' => 2.75,
        'available' => false,
        'image' => fakePngUpload('olives.png'),
    ]);

    $response->assertOk()
        ->assertJsonPath('data.ingredient.name', 'Black Olives')
        ->assertJsonPath('data.ingredient.image_url.public_id', 'api-restaurant/ingredients/olives');

    $this->assertDatabaseHas('ingredients', [
        'id' => $ingredient->id,
        'name' => 'Black Olives',
        'available' => false,
    ]);
});

it('prevents non admins from updating ingredients', function (): void {
    $user = User::factory()->create([
        'role' => User::ROLE_USER,
    ]);

    $ingredient = Ingredient::query()->create([
        'name' => 'Basil',
        'price' => 1.15,
        'ingredient' => 'herb',
        'available' => true,
    ]);

    Sanctum::actingAs($user);

    $response = putJson("/api/ingredients/{$ingredient->id}", [
        'name' => 'Fresh Basil',
    ]);

    $response->assertForbidden();
});
