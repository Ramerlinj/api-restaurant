<?php

namespace App\Modules\Orders\Models;

use App\Modules\Menu\Models\Pizza;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'order_items';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'pizza_id',
        'quantity',
        'unit_price',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function pizza(): BelongsTo
    {
        return $this->belongsTo(Pizza::class);
    }
}
