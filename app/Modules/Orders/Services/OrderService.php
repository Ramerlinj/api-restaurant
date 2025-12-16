<?php

namespace App\Modules\Orders\Services;

use App\Models\User;
use App\Modules\Locations\Models\Address;
use App\Modules\Menu\Models\Pizza;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class OrderService
{
    public function __construct(private readonly DatabaseManager $db)
    {
    }

    public function list(User $user, bool $includeAll = false): Collection
    {
        $query = Order::query()
            ->with(['items.pizza', 'address.city', 'payments'])
            ->orderByDesc('created_at');

        if (!($includeAll && $user->hasAdminPrivileges())) {
            $query->where('user_id', $user->getKey());
        }

        return $query->get();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function store(User $user, array $payload): Order
    {
        return $this->db->transaction(function () use ($user, $payload) {
            $addressId = $this->resolveAddress($user, $payload);

            $order = Order::create([
                'user_id' => $user->getKey(),
                'address_id' => $addressId,
                'total' => 0,
                'status' => Order::STATUS_PENDING,
            ]);

            $total = 0;

            foreach ($payload['items'] as $item) {
                $pizza = Pizza::findOrFail($item['pizza_id']);
                $quantity = (int) ($item['quantity'] ?? 1);
                $quantity = max($quantity, 1);

                OrderItem::create([
                    'order_id' => $order->getKey(),
                    'pizza_id' => $pizza->getKey(),
                    'quantity' => $quantity,
                    'unit_price' => $pizza->price,
                ]);

                $total += (float) $pizza->price * $quantity;
            }

            $order->total = $total;
            $order->save();

            return $order->load(['items.pizza', 'address.city', 'payments']);
        });
    }

    public function show(User $user, Order $order): Order
    {
        $this->ensureCanAccessOrder($user, $order);

        return $order->load(['items.pizza', 'address.city', 'payments']);
    }

    public function updateStatus(User $actor, Order $order, string $status): Order
    {
        $this->ensureAdmin($actor);

        return $this->db->transaction(function () use ($order, $status) {
            $order->status = $status;
            $order->save();

            return $order->load(['items.pizza', 'address.city', 'payments']);
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function resolveAddress(User $user, array $payload): int
    {
        $addressId = Arr::get($payload, 'address_id');

        if ($addressId) {
            $address = Address::query()
                ->whereKey($addressId)
                ->where('user_id', $user->getKey())
                ->first();

            if (!$address) {
                throw new AuthorizationException('Address does not belong to this user.');
            }

            return (int) $address->getKey();
        }

        $addressData = Arr::get($payload, 'address', []);
        $addressData['user_id'] = $user->getKey();

        $address = Address::create($addressData);

        return (int) $address->getKey();
    }

    private function ensureCanAccessOrder(User $user, Order $order): void
    {
        if ($user->hasAdminPrivileges()) {
            return;
        }

        if ($order->user_id !== $user->getKey()) {
            throw new AuthorizationException('You are not allowed to access this order.');
        }
    }

    private function ensureAdmin(?User $actor): void
    {
        if (!($actor?->hasAdminPrivileges())) {
            throw new AuthorizationException('Only admins can perform this action.');
        }
    }
}
