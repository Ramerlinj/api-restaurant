<?php

namespace App\Modules\Menu\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Menu\Models\Ingredient;
use App\Modules\Menu\Requests\StoreIngredientRequest;
use App\Modules\Menu\Requests\UpdateIngredientRequest;
use App\Modules\Menu\Services\IngredientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IngredientController extends Controller
{
    public function __construct(private readonly IngredientService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $ingredients = $this->service->list();

        return response()->json([
            'data' => [
                'ingredients' => $ingredients,
            ],
        ]);
    }

    public function store(StoreIngredientRequest $request): JsonResponse
    {
        $ingredient = $this->service->store($request->validated());

        return response()->json([
            'data' => [
                'ingredient' => $ingredient,
            ],
        ], Response::HTTP_CREATED);
    }

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
