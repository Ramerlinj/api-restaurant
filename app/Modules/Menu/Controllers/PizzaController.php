<?php

namespace App\Modules\Menu\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Menu\Models\Pizza;
use App\Modules\Menu\Requests\StorePizzaRequest;
use App\Modules\Menu\Requests\UpdatePizzaRequest;
use App\Modules\Menu\Services\PizzaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class PizzaController extends Controller
{
    public function __construct(private readonly PizzaService $service)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/pizzas",
     *     tags={"Pizzas"},
     *     summary="Lista las pizzas disponibles",
     *     @OA\Response(response=200, description="Listado de pizzas", @OA\JsonContent(ref="#/components/schemas/PizzaCollectionResponse"))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $pizzas = $this->service->list();

        return response()->json([
            'data' => [
                'pizzas' => $pizzas,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/pizzas",
     *     tags={"Pizzas"},
     *     summary="Crea una pizza",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","price","image"},
     *                 @OA\Property(property="name", type="string", maxLength=100),
     *                 @OA\Property(property="description", type="string", nullable=true),
     *                 @OA\Property(property="price", type="number", format="float", minimum=0),
     *                 @OA\Property(property="is_recommended", type="boolean", default=false),
     *                 @OA\Property(property="image", type="string", format="binary", description="Imagen principal de la pizza")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Pizza creada", @OA\JsonContent(ref="#/components/schemas/PizzaResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StorePizzaRequest $request): JsonResponse
    {
        $pizza = $this->service->store($request->validated());

        return response()->json([
            'data' => [
                'pizza' => $pizza,
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/pizzas/{pizza}",
     *     tags={"Pizzas"},
     *     summary="Actualiza una pizza",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="pizza", in="path", required=true, description="ID de la pizza", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", maxLength=100),
     *                 @OA\Property(property="description", type="string", nullable=true),
     *                 @OA\Property(property="price", type="number", format="float", minimum=0),
     *                 @OA\Property(property="is_recommended", type="boolean"),
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Pizza actualizada", @OA\JsonContent(ref="#/components/schemas/PizzaResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(UpdatePizzaRequest $request, Pizza $pizza): JsonResponse
    {
        $updated = $this->service->update($pizza, $request->validated());

        return response()->json([
            'data' => [
                'pizza' => $updated,
            ],
        ]);
    }
}
