<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderItem;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function created(OrderItem $orderItem)
    {
        $order = $orderItem->order;
        $order->updateColumns();
    }

    /**
     * Handle the OrderItem "updated" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function updated(OrderItem $orderItem)
    {
        $order = $orderItem->order;
        if(!is_null($order)) {
            $order->updateColumns();
        }
    }

    /**
     * Handle the OrderItem "deleted" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function deleted(OrderItem $orderItem)
    {
        $order = $orderItem->order->withTrashed()->first();
        $order->updateColumns();
    }

    /**
     * Handle the OrderItem "restored" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function restored(OrderItem $orderItem)
    {
        $order = $orderItem->order;
        $order->updateColumns();
    }

    /**
     * Handle the OrderItem "force deleted" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function forceDeleted(OrderItem $orderItem)
    {
        $order = $orderItem->order->withTrashed()->first();
        $order->updateColumns();
    }
}
