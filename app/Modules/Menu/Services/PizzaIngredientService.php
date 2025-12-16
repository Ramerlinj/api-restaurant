<?php

namespace App\Modules\Menu\Services;

use App\Models\User;
use App\Modules\Menu\Models\Ingredient;
use App\Modules\Menu\Models\Pizza;
use App\Modules\Menu\Models\PizzaIngredient;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class PizzaIngredientService
{
    public function __construct(private readonly DatabaseManager $db)
    {
    }

    public function list(Pizza $pizza): Collection
    {
        return PizzaIngredient::query()
            ->with('ingredient')
            ->where('pizza_id', $pizza->getKey())
            ->orderBy('id')
            ->get();
    }

    public function attach(User $actor, Pizza $pizza, Ingredient $ingredient): PizzaIngredient
    {
        $this->ensureAdmin($actor);

        return $this->db->transaction(function () use ($pizza, $ingredient) {
            $pivot = PizzaIngredient::create([
                'pizza_id' => $pizza->getKey(),
                'ingredient_id' => $ingredient->getKey(),
            ]);

            return $pivot->load('ingredient');
        });
    }

    public function detach(User $actor, Pizza $pizza, PizzaIngredient $pivot): void
    {
        $this->ensureAdmin($actor);

        if ($pivot->pizza_id !== $pizza->getKey()) {
            throw new AuthorizationException('Ingredient does not belong to this pizza.');
        }

        $this->db->transaction(static fn() => $pivot->delete());
    }

    private function ensureAdmin(?User $actor): void
    {
        if (!($actor?->hasAdminPrivileges())) {
            throw new AuthorizationException('Only admins can modify pizza ingredients.');
        }
    }
}
