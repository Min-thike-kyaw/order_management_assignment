<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->attributes['item_code'] = 'product_'.Str::random(11);
        });
    }

    // scopes
    public function scopeFilter($query,QueryFilter $filter)
    {
        return $filter->apply($query);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
    // relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
