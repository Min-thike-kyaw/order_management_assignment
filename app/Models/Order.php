<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Filters\QueryFilter;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes, SecureDeletes;

    protected $guarded = [];


    // Functions
    protected static function booted()
    {
        static::creating(function ($order) {
            $lastId = (self::latest()->first()->id ?? 0)+1;
            $order->attributes['order_no'] = strtoupper(Str::random(2)). "-". str_pad($lastId, 8, 0, STR_PAD_LEFT);
            $order->attributes['grand_total'] = $order->attributes['total_amount'] - $order->attributes['total_discount'];
        });
        static::updating(function ($order) {
            $order->attributes['grand_total'] = $order->attributes['total_amount'] - $order->attributes['total_discount'];
        });
    }

    public function updateColumns()
    {
        $this->total_qty = $this->orderItems()->sum('qty');
        $this->total_amount = $this->orderItems()->sum('amount');
        $this->save();
    }

    // Scopes
    public function scopeFilter($query, QueryFilter $filter)
    {
        return $filter->apply($query);
    }
    public function scopeValid($query)
    {
        return $query->where('status', false);
    }
    public function scopeInvalid($query)
    {
        return $query->where('status', true);
    }

    // Relationships
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
