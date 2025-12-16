<?php

namespace App\Modules\Orders\Models;

use App\Models\User;
use App\Modules\Locations\Models\Address;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const AVAILABLE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PAID,
        self::STATUS_PREPARING,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'orders';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'address_id',
        'total',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
