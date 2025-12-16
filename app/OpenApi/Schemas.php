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
 *     schema="PizzaIngredientResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=501),
 *     @OA\Property(property="pizza_id", type="integer", example=10),
 *     @OA\Property(property="ingredient_id", type="integer", example=3),
 *     @OA\Property(property="ingredient", ref="#/components/schemas/IngredientResource")
 * )
 *
 * @OA\Schema(
 *     schema="PizzaIngredientResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="pizza_ingredient", ref="#/components/schemas/PizzaIngredientResource")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PizzaIngredientCollectionResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="ingredients",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/PizzaIngredientResource")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="CityResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=101),
 *     @OA\Property(property="name", type="string", example="Santo Domingo")
 * )
 *
 * @OA\Schema(
 *     schema="CityResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="city", ref="#/components/schemas/CityResource")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="CityCollectionResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="cities",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/CityResource")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AddressResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=9001),
 *     @OA\Property(property="address_line", type="string", example="Av. Central #123"),
 *     @OA\Property(property="sector", type="string", nullable=true, example="Zona Colonial"),
 *     @OA\Property(property="reference", type="string", nullable=true, example="Frente al parque"),
 *     @OA\Property(property="city_id", type="integer", example=2)
 * )
 *
 * @OA\Schema(
 *     schema="OrderItemResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1001),
 *     @OA\Property(property="pizza_id", type="integer", example=15),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="unit_price", type="string", example="12.90"),
 *     @OA\Property(property="pizza", ref="#/components/schemas/PizzaResource")
 * )
 *
 * @OA\Schema(
 *     schema="PaymentResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=77),
 *     @OA\Property(property="amount", type="string", example="25.80"),
 *     @OA\Property(property="status", type="string", example="completed"),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="OrderResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=5000),
 *     @OA\Property(property="total", type="string", example="25.80"),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="address", ref="#/components/schemas/AddressResource"),
 *     @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/OrderItemResource")),
 *     @OA\Property(property="payments", type="array", @OA\Items(ref="#/components/schemas/PaymentResource"))
 * )
 *
 * @OA\Schema(
 *     schema="OrderResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="order", ref="#/components/schemas/OrderResource")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="OrderCollectionResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="orders",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/OrderResource")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaymentResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="payment", ref="#/components/schemas/PaymentResource")
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
