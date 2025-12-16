<?php

namespace App\Modules\Payments\Services;

use App\Models\User;
use App\Modules\Orders\Models\Order;
use App\Modules\Payments\Models\Payment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\DatabaseManager;

class PaymentService
{
    public function __construct(private readonly DatabaseManager $db)
    {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function store(User $actor, Order $order, array $payload): Payment
    {
        $this->ensureCanAccessOrder($actor, $order);

        if ($order->status === Order::STATUS_CANCELLED) {
            throw new AuthorizationException('Cannot add payments to cancelled orders.');
        }

        return $this->db->transaction(function () use ($order, $payload) {
            $payment = Payment::create([
                'order_id' => $order->getKey(),
                'amount' => $payload['amount'],
                'status' => $payload['status'] ?? Payment::STATUS_COMPLETED,
            ]);

            if ($payment->status === Payment::STATUS_COMPLETED && (float) $payment->amount >= (float) $order->total) {
                $order->status = Order::STATUS_PAID;
                $order->save();
            }

            return $payment->load('order');
        });
    }

    private function ensureCanAccessOrder(User $user, Order $order): void
    {
        if ($user->hasAdminPrivileges()) {
            return;
        }

        if ($order->user_id !== $user->getKey()) {
            throw new AuthorizationException('You are not allowed to manage this order.');
        }
    }
}
