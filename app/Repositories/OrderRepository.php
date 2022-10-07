<?php

namespace App\Repositories;

use Exception;
use App\Models\Order;
use App\Filters\OrderFilter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\OrderItem;
use App\Models\Product;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAllOrders(){
        $result['success'] = true;
        try{
            $orders = OrderResource::collection(Order::filter(app(OrderFilter::class))->paginate(request('per_page') ?? config('emart.default_paginate')));
            $result['data'] = $orders;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function getOrderById($orderId)
    {
        $result['success'] = true;
        try{
            $order = new OrderResource(Order::with('orderItems', 'customer')->findOrFail($orderId));
            $result['data'] = $order;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }



    public function createOrder(array $orderDetails)
    {
        $result['success'] = true;
        // Checking available products
        $productIds = array_column($orderDetails['order_items'], 'product_id');
        $availableCounts = Product::available()->whereIn('id', $productIds)->count();
        if($availableCounts == 0) {
            return ['success' => false, 'message' => "All of products are not available", 'code' => 500];
        }

        // Store Process
        DB::beginTransaction();

        try{
            // Save Order
            $order = new Order();
            $order->total_qty = 0;
            $order->total_amount = 0;
            $order->total_discount = $orderDetails['total_discount'];
            $order->remark = $orderDetails['remark'] ?? NULL;
            $order->customer_id = $orderDetails['customer_id'];
            $order->save();

            // Store Order Items
            foreach($orderDetails['order_items'] as $order_item_array) {
                $product = Product::available()->find($order_item_array['product_id']);

                // Store Only Available Products
                if(! is_null( $product)) {
                    $order_item = new OrderItem();
                    $order_item->order_id = $order->id;
                    $order_item->product_id = $product->id;
                    $order_item->qty = $order_item_array['qty'];
                    $order_item->price = $order_item_array['price'];
                    $order_item->unit_price = $product->price;
                    $order_item->amount = $order_item_array['qty'] * $order_item_array['price'];
                    $order_item->saveQuietly(); // save without affecting on events
                }
            }

            // Update Order
            $order->updateColumns();

            $result['data'] = new OrderResource($order->load('customer','orderItems'));
        } catch (Exception $e) {
            DB::rollBack(); // Rollback for database error
            return ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        DB::commit();
        return $result;


    }
    public function updateOrder($orderId, array $newDetails)
    {
        $result['success'] = true;
        try{
            $order = Order::findOrFail($orderId);
            $order->update($newDetails);
            $result['data'] = new OrderResource($order);
        } catch (Exception $e){
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
    public function deleteOrder($orderId)
    {
        $result['success'] = true;
        try{
            Order::findOrFail($orderId)->delete();
            $result['data'] = null;
            $result['code'] = Response::HTTP_NO_CONTENT;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
    public function addItem($orderId, $orderDetails)
    {
        $result['success'] = true;
        try{
            $orderItem = OrderItem::create($orderDetails+ ['order_id' => $orderId]);
            if($orderItem->id){
                $result['data'] =  new OrderResource($orderItem->order->load('orderItems'));
            } else {
                $result = ['success' => false, 'message' => "Item cannot be added because of order status or product.", 'code' => 500];

            }
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
    public function addMultipleItems($id, $orderDetails)
    {
        $result['success'] = true;
        // checking if order is valid
        $order = Order::valid()->find($id);
        if(is_null($order)) {
            return ['success' => false, 'message' => 'Item cannot be added. Because order status is true', 'code' => 419];
        }
        // Checking available products
        $productIds = array_column($orderDetails['order_items'], 'product_id');
        $availableCounts = Product::available()->whereIn('id', $productIds)->count();
        if($availableCounts == 0) {
            return ['success' => false, 'message' => "All of products are not available", 'code' => 500];
        }

        // Store Process
        DB::beginTransaction();
        try{


            // Store Order Items
            foreach($orderDetails['order_items'] as $order_item_array) {
                $product = Product::available()->find($order_item_array['product_id']);

                // Store Only Available Products
                if(! is_null( $product)) {
                    $order_item = new OrderItem();
                    $order_item->order_id = $order->id;
                    $order_item->product_id = $product->id;
                    $order_item->qty = $order_item_array['qty'];
                    $order_item->price = $order_item_array['price'];
                    $order_item->unit_price = $product->price;
                    $order_item->amount = $order_item_array['qty'] * $order_item_array['price'];
                    $order_item->saveQuietly(); // save without affecting on events
                }
            }
            // Update Order
            $order->updateColumns();

            $result['data'] = new OrderResource($order->load('orderItems'));
        } catch (Exception $e) {
            DB::rollBack(); //Rollback for some errors
            return ['success' => false, 'message' => $e->getMessage(), 'code' => 500];

        }
        DB::commit();
        return $result;

    }
    public function removeItems($orderId, $orderDetails)
    {
        $result['success'] = true;
        try{
            $order = Order::valid()->find($orderId);
            if(is_null($order)) {
                return ['success' => false, 'message' => 'Item cannot be removed. Because order status is true', 'code' => 419];
            }

            OrderItem::whereIn('id', $orderDetails['order_item_ids'])->where('order_id', $orderId)->delete();

            $order->updateColumns();

            $result['data'] = new OrderResource($order->load('orderItems'));
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
    public function restoreOrder($orderId)
    {
        try{
            $order = Order::withTrashed()->findOrFail($orderId);
            $order->restore();
            $order->updateColumns();
            $result = ['success' => true, 'data' => new OrderResource($order)];
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function forceDeleteOrder($orderId)
    {
        try {
            $order = Order::withTrashed()->findOrFail($orderId);
            if(!$order->canSecureDelete('orderItems')) {
                return ['success' => false, 'message' => "Cannot delete. Remove all the related associations.", 'code' => 419];
            }
            $order->forceDeleteWithRelations('orderItems');
            $result = ['success' => true, 'data' => NULL, 'code' => Response::HTTP_NO_CONTENT];
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

}
