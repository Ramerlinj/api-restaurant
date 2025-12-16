<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Restaurant",
 *     description="REST API para autenticación de usuarios, administración del menú y gestión de ingredientes."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor principal"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum Token",
 *     description="Incluye el token generado en el login o registro usando el formato: Bearer {token}"
 * )
 *
 * @OA\Tag(name="Auth", description="Endpoints de autenticación y perfil")
 * @OA\Tag(name="Users", description="Administración de usuarios")
 * @OA\Tag(name="Ingredients", description="Gestión de ingredientes del menú")
 * @OA\Tag(name="Pizzas", description="Gestión de pizzas del menú")
 * @OA\Tag(name="Cities", description="Administración de ciudades disponibles para direcciones")
 * @OA\Tag(name="Orders", description="Gestión de órdenes de compra")
 * @OA\Tag(name="Payments", description="Registro de pagos asociados a órdenes")
 */
class SwaggerDoc
{
}
