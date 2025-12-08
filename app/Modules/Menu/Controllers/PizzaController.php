<?php

namespace App\Modules\Menu\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Menu\Models\Pizza;
use App\Modules\Menu\Requests\StorePizzaRequest;
use App\Modules\Menu\Requests\UpdatePizzaRequest;
use App\Modules\Menu\Services\PizzaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PizzaController extends Controller
{
    public function __construct(private readonly PizzaService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $pizzas = $this->service->list();

        return response()->json([
            'data' => [
                'pizzas' => $pizzas,
            ],
        ]);
    }

    public function store(StorePizzaRequest $request): JsonResponse
    {
        $pizza = $this->service->store($request->validated());

        return response()->json([
            'data' => [
                'pizza' => $pizza,
            ],
        ], Response::HTTP_CREATED);
    }

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
