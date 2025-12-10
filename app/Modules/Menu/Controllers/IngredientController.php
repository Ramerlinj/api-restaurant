<?php

namespace App\Modules\Menu\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Menu\Models\Ingredient;
use App\Modules\Menu\Requests\StoreIngredientRequest;
use App\Modules\Menu\Requests\UpdateIngredientRequest;
use App\Modules\Menu\Services\IngredientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class IngredientController extends Controller
{
    public function __construct(private readonly IngredientService $service)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/ingredients",
     *     tags={"Ingredients"},
     *     summary="Lista los ingredientes disponibles",
     *     @OA\Response(response=200, description="Listado de ingredientes", @OA\JsonContent(ref="#/components/schemas/IngredientCollectionResponse"))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $ingredients = $this->service->list();

        return response()->json([
            'data' => [
                'ingredients' => $ingredients,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/ingredients",
     *     tags={"Ingredients"},
     *     summary="Crea un ingrediente",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","price","ingredient","image"},
     *                 @OA\Property(property="name", type="string", maxLength=100),
     *                 @OA\Property(property="price", type="number", format="float", minimum=0),
     *                 @OA\Property(property="ingredient", type="string", maxLength=50, description="Identificador interno"),
     *                 @OA\Property(property="available", type="boolean", default=false),
     *                 @OA\Property(property="image", type="string", format="binary", description="Imagen del ingrediente")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Ingrediente creado", @OA\JsonContent(ref="#/components/schemas/IngredientResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StoreIngredientRequest $request): JsonResponse
    {
        $ingredient = $this->service->store($request->validated());

        return response()->json([
            'data' => [
                'ingredient' => $ingredient,
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/ingredients/{ingredient}",
     *     tags={"Ingredients"},
     *     summary="Actualiza un ingrediente",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="ingredient", in="path", required=true, description="ID del ingrediente", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", maxLength=100),
     *                 @OA\Property(property="price", type="number", format="float", minimum=0),
     *                 @OA\Property(property="ingredient", type="string", maxLength=50),
     *                 @OA\Property(property="available", type="boolean"),
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Ingrediente actualizado", @OA\JsonContent(ref="#/components/schemas/IngredientResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(UpdateIngredientRequest $request, Ingredient $ingredient): JsonResponse
    {
        $updated = $this->service->update($ingredient, $request->validated());

        return response()->json([
            'data' => [
                'ingredient' => $updated,
            ],
        ]);
    }
}
