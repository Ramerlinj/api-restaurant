<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Orders\Models\Order;
use App\Modules\Payments\Requests\StorePaymentRequest;
use App\Modules\Payments\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $service)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/orders/{order}/payments",
     *     tags={"Payments"},
     *     summary="Registra un pago para la orden",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="order", in="path", required=true, description="ID de la orden", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="number", format="float", minimum=0.01),
     *             @OA\Property(property="status", type="string", enum={"pending","completed","failed"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Pago registrado", @OA\JsonContent(ref="#/components/schemas/PaymentResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StorePaymentRequest $request, Order $order): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $payment = $this->service->store($user, $order, $request->validated());

        return response()->json([
            'data' => [
                'payment' => $payment,
            ],
        ], Response::HTTP_CREATED);
    }
}
