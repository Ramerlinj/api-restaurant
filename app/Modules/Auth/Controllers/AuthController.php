<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Requests\UpdateProfileRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $service)
    {
    }

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

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'user' => $request->user(),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request->user());

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->service->update($request->user(), $request->validated());

        return response()->json([
            'data' => [
                'user' => $user,
            ],
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $this->service->delete($request->user());

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
