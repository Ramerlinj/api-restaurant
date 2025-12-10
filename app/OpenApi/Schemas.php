<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * Colección de esquemas reutilizables para las respuestas de la API.
 *
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=42),
 *     @OA\Property(property="name", type="string", example="Ada"),
 *     @OA\Property(property="surname", type="string", example="Lovelace"),
 *     @OA\Property(property="email", type="string", format="email", example="ada@example.com"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+34 600 123 123"),
 *     @OA\Property(property="role", type="string", example="user"),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="AuthTokenResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 *         @OA\Property(property="access_token", type="string", example="1|ZxC..."),
 *         @OA\Property(property="token_type", type="string", example="Bearer")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UserResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="user", ref="#/components/schemas/UserResource")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="IngredientImage",
 *     type="object",
 *     nullable=true,
 *     @OA\Property(property="public_id", type="string", example="api-restaurant/ingredients/abc123"),
 *     @OA\Property(property="secure_url", type="string", format="uri", example="https://res.cloudinary.com/demo/image/upload/v1/ingredients.png"),
 *     @OA\Property(property="url", type="string", format="uri"),
 *     @OA\Property(property="format", type="string", example="png"),
 *     @OA\Property(property="resource_type", type="string", example="image"),
 *     @OA\Property(property="bytes", type="integer", example=450123),
 *     @OA\Property(property="width", type="integer", example=1200),
 *     @OA\Property(property="height", type="integer", example=800)
 * )
 *
 * @OA\Schema(
 *     schema="IngredientResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="name", type="string", example="Mozzarella"),
 *     @OA\Property(property="price", type="string", example="2.50"),
 *     @OA\Property(property="ingredient", type="string", example="cheese"),
 *     @OA\Property(property="available", type="boolean", example=true),
 *     @OA\Property(property="image_url", ref="#/components/schemas/IngredientImage")
 * )
 *
 * @OA\Schema(
 *     schema="IngredientResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="ingredient", ref="#/components/schemas/IngredientResource")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="IngredientCollectionResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="ingredients",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/IngredientResource")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PizzaImage",
 *     type="object",
 *     nullable=true,
 *     @OA\Property(property="public_id", type="string"),
 *     @OA\Property(property="secure_url", type="string", format="uri"),
 *     @OA\Property(property="url", type="string", format="uri"),
 *     @OA\Property(property="format", type="string", example="jpg"),
 *     @OA\Property(property="resource_type", type="string", example="image"),
 *     @OA\Property(property="bytes", type="integer"),
 *     @OA\Property(property="width", type="integer"),
 *     @OA\Property(property="height", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="PizzaResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=99),
 *     @OA\Property(property="name", type="string", example="Diavola"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="price", type="string", example="12.90"),
 *     @OA\Property(property="is_recommended", type="boolean", example=true),
 *     @OA\Property(property="image_url", ref="#/components/schemas/PizzaImage")
 * )
 *
 * @OA\Schema(
 *     schema="PizzaResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="pizza", ref="#/components/schemas/PizzaResource")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PizzaCollectionResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="pizzas",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/PizzaResource")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         additionalProperties=@OA\Schema(type="array", @OA\Items(type="string")),
 *         example={"email":{"Las credenciales no coinciden."}}
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UnauthorizedError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Unauthenticated."),
 *     @OA\Property(property="status", type="integer", example=401)
 * )
 *
 * @OA\Schema(
 *     schema="ForbiddenError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="This action is unauthorized."),
 *     @OA\Property(property="status", type="integer", example=403)
 * )
 */
class Schemas
{
}
