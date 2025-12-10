<?php

namespace App\Modules\Locations\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Locations\Models\City;
use App\Modules\Locations\Requests\StoreCityRequest;
use App\Modules\Locations\Requests\UpdateCityRequest;
use App\Modules\Locations\Services\CityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class CityController extends Controller
{
    public function __construct(private readonly CityService $service)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/cities",
     *     tags={"Cities"},
     *     summary="Lista todas las ciudades disponibles",
     *     @OA\Response(response=200, description="Listado de ciudades", @OA\JsonContent(ref="#/components/schemas/CityCollectionResponse"))
     * )
     */
    public function index(): JsonResponse
    {
        $cities = $this->service->list();

        return response()->json([
            'data' => [
                'cities' => $cities,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/cities",
     *     tags={"Cities"},
     *     summary="Crea una ciudad",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id","name"},
     *             @OA\Property(property="id", type="integer", minimum=1),
     *             @OA\Property(property="name", type="string", maxLength=120)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Ciudad creada", @OA\JsonContent(ref="#/components/schemas/CityResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StoreCityRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $city = $this->service->store($user, $request->validated());

        return response()->json([
            'data' => [
                'city' => $city,
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/cities/{city}",
     *     tags={"Cities"},
     *     summary="Actualiza el nombre de una ciudad",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="city", in="path", description="ID de la ciudad", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=120)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Ciudad actualizada", @OA\JsonContent(ref="#/components/schemas/CityResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(UpdateCityRequest $request, City $city): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $updated = $this->service->update($user, $city, $request->validated());

        return response()->json([
            'data' => [
                'city' => $updated,
            ],
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/cities/{city}",
     *     tags={"Cities"},
     *     summary="Elimina una ciudad",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="city", in="path", description="ID de la ciudad", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Ciudad eliminada"),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError"))
     * )
     */
    public function destroy(Request $request, City $city): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $this->service->delete($user, $city);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
