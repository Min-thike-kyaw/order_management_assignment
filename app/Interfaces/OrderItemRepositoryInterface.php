<?php

namespace App\Interfaces;

interface OrderItemRepositoryInterface
{
    public function deleteOrderItem($orderItemId);
    public function createOrderItem(array $orderItemDetails);
    public function updateOrderItem($orderItemId, array $newDetails);
    public function restoreOrderItem($orderItemId);
    public function forceDeleteOrderItem($orderItemId);
}
