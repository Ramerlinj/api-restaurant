<?php

namespace App\Modules\Menu\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Menu\Models\Ingredient;
use App\Modules\Menu\Models\Pizza;
use App\Modules\Menu\Models\PizzaIngredient;
use App\Modules\Menu\Requests\StorePizzaIngredientRequest;
use App\Modules\Menu\Services\PizzaIngredientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class PizzaIngredientController extends Controller
{
    public function __construct(private readonly PizzaIngredientService $service)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/pizzas/{pizza}/ingredients",
     *     tags={"Pizzas"},
     *     summary="Lista los ingredientes asociados a una pizza",
     *     @OA\Parameter(name="pizza", in="path", required=true, description="ID de la pizza", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Listado de ingredientes", @OA\JsonContent(ref="#/components/schemas/PizzaIngredientCollectionResponse"))
     * )
     */
    public function index(Pizza $pizza): JsonResponse
    {
        $ingredients = $this->service->list($pizza);

        return response()->json([
            'data' => [
                'ingredients' => $ingredients,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/pizzas/{pizza}/ingredients",
     *     tags={"Pizzas"},
     *     summary="Asocia un ingrediente a la pizza",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="pizza", in="path", required=true, description="ID de la pizza", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ingredient_id"},
     *             @OA\Property(property="ingredient_id", type="integer", minimum=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Ingrediente vinculado", @OA\JsonContent(ref="#/components/schemas/PizzaIngredientResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StorePizzaIngredientRequest $request, Pizza $pizza): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $ingredient = Ingredient::findOrFail($request->integer('ingredient_id'));
        $pivot = $this->service->attach($user, $pizza, $ingredient);

        return response()->json([
            'data' => [
                'pizza_ingredient' => $pivot,
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/pizzas/{pizza}/ingredients/{pivot}",
     *     tags={"Pizzas"},
     *     summary="Elimina la relación entre una pizza y un ingrediente",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="pizza", in="path", required=true, description="ID de la pizza", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="pivot", in="path", required=true, description="ID del registro pizza_ingredient", @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Ingrediente desvinculado"),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError"))
     * )
     */
    public function destroy(Request $request, Pizza $pizza, PizzaIngredient $pivot): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $this->service->detach($user, $pizza, $pivot);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
