<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Users\Requests\UpdateUserRequest;
use App\Modules\Users\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(private readonly UserService $service)
    {
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $updated = $this->service->update($request->user(), $user, $request->validated());

        return response()->json([
            'data' => [
                'user' => $updated,
            ],
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->service->delete($request->user(), $user);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
