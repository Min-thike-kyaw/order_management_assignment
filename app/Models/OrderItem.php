<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    // Functions
    protected static function booted()
    {
        static::creating(function ($order_item) {
            $order = Order::valid()->find($order_item->attributes['order_id']);
            $product = Product::available()->find($order_item->attributes['product_id']);
            if(is_null($order) || is_null($product)){
                return false;
            } else {
                $order_item->attributes['unit_price'] = Product::find($order_item->attributes['product_id'])->price;
                $order_item->attributes['amount'] = $order_item->attributes['price'] * $order_item->attributes['qty'];
            }
        });

        static::updating(function ($order_item) {
            $order = Order::valid()->find($order_item->attributes['order_id']);
            $product = Product::available()->find($order_item->attributes['product_id']);
            if(is_null($order)|| is_null($product)){
                return false;
            }
        });

        static::restoring(function ($order_item) {
            $order = Order::valid()->find($order_item->attributes['order_id']);
            if(is_null($order)){
                return false;
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class)->withDefault();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }
}
