<?php

namespace App\Modules\Menu\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PizzaIngredient extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $table = 'pizza_ingredients';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'pizza_id',
        'ingredient_id',
    ];

    /**
     * Relación con la pizza base.
     */
    public function pizza(): BelongsTo
    {
        return $this->belongsTo(Pizza::class);
    }

    /**
     * Relación con el ingrediente asociado.
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
