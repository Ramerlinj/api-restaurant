<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Requests\UpdateProfileRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $service)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="Inicia sesión y genera un token Sanctum",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="device_name", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Inicio de sesión correcto", @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")),
     *     @OA\Response(response=422, description="Credenciales inválidas", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $payload = $this->service->authenticate(
            $data['email'],
            $data['password'],
            $data['device_name'] ?? null,
        );

        return response()->json([
            'data' => [
                'user' => $payload['user'],
                'access_token' => $payload['access_token'],
                'token_type' => $payload['token_type'],
            ],
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Auth"},
     *     summary="Registra un usuario y devuelve su token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","surname","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", maxLength=100),
     *             @OA\Property(property="surname", type="string", maxLength=100),
     *             @OA\Property(property="email", type="string", format="email", maxLength=150),
     *             @OA\Property(property="password", type="string", format="password", minLength=8),
     *             @OA\Property(property="password_confirmation", type="string", format="password", minLength=8),
     *             @OA\Property(property="phone", type="string", nullable=true, maxLength=30),
     *             @OA\Property(property="device_name", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Usuario registrado", @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $this->service->register($request->validated());

        return response()->json([
            'data' => [
                'user' => $payload['user'],
                'access_token' => $payload['access_token'],
                'token_type' => $payload['token_type'],
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     tags={"Auth"},
     *     summary="Obtiene el usuario autenticado",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Usuario autenticado", @OA\JsonContent(ref="#/components/schemas/UserResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError"))
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'user' => $request->user(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Auth"},
     *     summary="Revoca el token actual",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=204, description="Sesión finalizada"),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError"))
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request->user());

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Put(
     *     path="/api/auth/me",
     *     tags={"Auth"},
     *     summary="Actualiza el perfil del usuario autenticado",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=100),
     *             @OA\Property(property="surname", type="string", maxLength=100),
     *             @OA\Property(property="email", type="string", format="email", maxLength=150),
     *             @OA\Property(property="phone", type="string", nullable=true, maxLength=30),
     *             @OA\Property(property="password", type="string", format="password", minLength=8),
     *             @OA\Property(property="password_confirmation", type="string", format="password", minLength=8),
     *             @OA\Property(property="role", type="string", description="Solo superadmins pueden cambiarlo", enum={"user","admin","superadmin"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Perfil actualizado", @OA\JsonContent(ref="#/components/schemas/UserResponse")),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ForbiddenError")),
     *     @OA\Response(response=422, description="Datos inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->service->update($request->user(), $request->validated());

        return response()->json([
            'data' => [
                'user' => $user,
            ],
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/auth/me",
     *     tags={"Auth"},
     *     summary="Elimina la cuenta autenticada",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=204, description="Cuenta eliminada"),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/UnauthorizedError"))
     * )
     */
    public function destroy(Request $request): JsonResponse
    {
        $this->service->delete($request->user());

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
