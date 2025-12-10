<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Users\Requests\UpdateUserRequest;
use App\Modules\Users\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(private readonly UserService $service)
    {
    }

    /**
     * @OA\Put(
     *     path="/api/users/{user}",
     *     tags={"Users"},
     *     summary="Actualiza un usuario",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="user", in="path", required=true, description="ID del usuario a modificar", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=100),
     *             @OA\Property(property="surname", type="string", maxLength=100),
     *             @OA\Property(property="email", type="string", format="email", maxLength=150),
     *             @OA\Property(property="phone", type="string", nullable=true, maxLength=30),
     *             @OA\Property(property="password", type="string", format="password", minLength=8),
     *             @OA\Property(property="password_confirmation", type="string", format="password", minLength=8),
     *             @OA\Property(property="role", type="string", enum={"user","admin","superadmin"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Usuario actualizado", @OA\JsonContent(ref="#/components/schemas/UserResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $updated = $this->service->update($request->user(), $user, $request->validated());

        return response()->json([
            'data' => [
                'user' => $updated,
            ],
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{user}",
     *     tags={"Users"},
     *     summary="Elimina un usuario",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="user", in="path", required=true, description="ID del usuario", @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Usuario eliminado"),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError"))
     * )
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->service->delete($request->user(), $user);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
