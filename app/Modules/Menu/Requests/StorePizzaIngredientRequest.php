<?php

namespace App\Modules\Menu\Requests;

use App\Modules\Menu\Models\Pizza;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePizzaIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminPrivileges() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Pizza|null $pizza */
        $pizza = $this->route('pizza');
        $pizzaId = $pizza?->getKey() ?? 0;

        return [
            'ingredient_id' => [
                'required',
                'integer',
                Rule::exists('ingredients', 'id'),
                Rule::unique('pizza_ingredients', 'ingredient_id')->where(static function ($query) use ($pizzaId) {
                    return $query->where('pizza_id', $pizzaId);
                }),
            ],
        ];
    }
}
