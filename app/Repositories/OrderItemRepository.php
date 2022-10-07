<?php

namespace App\Repositories;

use Exception;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Filters\OrderFilter;
use App\Http\Resources\OrderItemResouce;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;
use App\Interfaces\OrderItemRepositoryInterface;

class OrderItemRepository implements OrderItemRepositoryInterface
{

    public function createOrderItem(array $orderItemDetails)
    {
        $result['success'] = true;

        try{
            $order = Order::valid()->find($orderItemDetails['order_id']);


            $orderItem = OrderItem::create($orderItemDetails);
            if($orderItem->id) {
                $result['data'] = new OrderItemResouce($orderItem);
            } else {
                $result = ['success' => false, 'message' => "Item cannot be added because of order status or product.", 'code' => 500];
            }
        } catch (Exception $e) {

            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;


    }
    public function updateOrderItem($orderId, array $newDetails)
    {
        $result['success'] = true;
        try{
            $orderItem = OrderItem::findOrFail($orderId);

            // $result['data'] = $orderItem->update($newDetails);
            // return $result;
            if($orderItem->update($newDetails)){
                $result['data'] = new OrderItemResouce($orderItem->load('order', 'product'));
            } else {
                $result = ['success' => false, 'message' => "Item cannot be edited because of order status or product.", 'code' => 500];
            }
        } catch (Exception $e){
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
    public function deleteOrderItem($orderId)
    {
        $result['success'] = true;
        try{
            $orderItem = OrderItem::findOrFail($orderId);
            // Check if order status is false
            if($orderItem->order->status) {
                return ['success' => false, 'message' => 'Item cannot be added. Because order status is true', 'code' => 419];
            }

            $orderItem->delete();
            $result['data'] = null;
            $result['code'] = Response::HTTP_NO_CONTENT;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function restoreOrderItem($orderItemId)
    {
        try{
            $orderItem = OrderItem::withTrashed()->findOrFail($orderItemId);

            if($orderItem->restore()){
                $result = ['success' => true, 'data' => new OrderItemResouce($orderItem)];
            } else {
                $result = ['success' => false, 'message' => "Order item cannot be restored", 'code' => 419];
            }
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function forceDeleteOrderItem($orderItemId)
    {
        try{
            $orderItem = OrderItem::withTrashed()->findOrFail($orderItemId);
            // check if it's deleted or not
            if(!is_null($orderItem)) {
                // if it's not deleted, check order status

                $order = Order::invalid()->find($orderItem->order_id);
                // return ['success' => true, 'data' => $order];

                // if order is not true, force delete
                if($order) {
                    $result = ['success' => false, 'message' => "Cannot delete. Order status is true.", 'code' => 419];
                } else {
                    $orderItem->forceDelete();
                    $result = ['success' => true, 'data' => NULL, 'code' => Response::HTTP_NO_CONTENT];
                }
            } else {
                // if it's deleted, allow permanent delete
                $orderItem = OrderItem::withTrashed()->findOrFail($orderItem);
                $orderItem->forceDelete();
                $result = ['success' => true, 'data' => NULL, 'code' => Response::HTTP_NO_CONTENT];
            }
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
}
