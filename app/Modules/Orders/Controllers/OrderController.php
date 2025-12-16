<?php

namespace App\Modules\Orders\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Requests\StoreOrderRequest;
use App\Modules\Orders\Requests\UpdateOrderStatusRequest;
use App\Modules\Orders\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $service)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Lista las órdenes del usuario autenticado o todas si es admin",
     *     @OA\Parameter(name="all", in="query", description="Solo admins: incluir todas las órdenes", @OA\Schema(type="boolean")),
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Listado de órdenes", @OA\JsonContent(ref="#/components/schemas/OrderCollectionResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError"))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $includeAll = $user->hasAdminPrivileges() && $request->boolean('all');

        $orders = $this->service->list($user, $includeAll);

        return response()->json([
            'data' => [
                'orders' => $orders,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Crea una nueva orden",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(property="address_id", type="integer", nullable=true),
     *             @OA\Property(property="address", type="object", nullable=true,
     *                 @OA\Property(property="address_line", type="string", maxLength=200),
     *                 @OA\Property(property="city_id", type="integer"),
     *                 @OA\Property(property="sector", type="string", nullable=true, maxLength=100),
     *                 @OA\Property(property="reference", type="string", nullable=true, maxLength=200)
     *             ),
     *             @OA\Property(property="items", type="array",
     *                 @OA\Items(
     *                     required={"pizza_id"},
     *                     @OA\Property(property="pizza_id", type="integer"),
     *                     @OA\Property(property="quantity", type="integer", minimum=1, default=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Orden creada", @OA\JsonContent(ref="#/components/schemas/OrderResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $order = $this->service->store($user, $request->validated());

        return response()->json([
            'data' => [
                'order' => $order,
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{order}",
     *     tags={"Orders"},
     *     summary="Obtiene una orden específica",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="order", in="path", required=true, description="ID de la orden", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Orden encontrada", @OA\JsonContent(ref="#/components/schemas/OrderResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError"))
     * )
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $order = $this->service->show($user, $order);

        return response()->json([
            'data' => [
                'order' => $order,
            ],
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{order}/status",
     *     tags={"Orders"},
     *     summary="Actualiza el estado de una orden",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="order", in="path", required=true, description="ID de la orden", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"pending","paid","preparing","delivered","cancelled"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Estado actualizado", @OA\JsonContent(ref="#/components/schemas/OrderResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError"))
     * )
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $order = $this->service->updateStatus($user, $order, $request->string('status')->toString());

        return response()->json([
            'data' => [
                'order' => $order,
            ],
        ]);
    }
}
