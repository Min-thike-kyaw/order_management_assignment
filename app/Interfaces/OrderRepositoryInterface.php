<?php

namespace App\Interfaces;

interface OrderRepositoryInterface
{
    public function getAllOrders();
    public function getOrderById($orderId);
    public function deleteOrder($orderId);
    public function createOrder(array $orderDetails);
    public function updateOrder($orderId, array $newDetails);
    public function addItem($orderId, $orderDetails);
    public function addMultipleItems($orderId, $orderDetails);
    public function removeItems($orderId, $orderDetails);
    public function restoreOrder($orderId);
    public function forceDeleteOrder($orderId);
}
