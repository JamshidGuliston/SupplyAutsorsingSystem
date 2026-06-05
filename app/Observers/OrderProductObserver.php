<?php

namespace App\Observers;

use App\Models\order_product;
use App\Services\Push\PushService;
use Throwable;

class OrderProductObserver
{
    public function __construct(private PushService $push) {}

    public function created(order_product $order): void
    {
        if (!$order->kingar_name_id) {
            return;
        }
        try {
            $this->push->sendToChefsOfKindgarden(
                (int) $order->kingar_name_id,
                'Yangi buyurtma',
                'Bog\'cha #' . $order->kingar_name_id . ' uchun yangi buyurtma: ' . ($order->order_title ?? '—'),
                ['kind' => 'new_order', 'order_id' => (string) $order->id]
            );
        } catch (Throwable $e) {
            \Log::error('OrderProductObserver push failed', ['error' => $e->getMessage(), 'order_id' => $order->id]);
        }
    }
}
