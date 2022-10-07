<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'phone_no', 'address', 'is_active', 'city_name', 'zone_name'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // scopes
    public function scopeFilter($query, QueryFilter $filter)
    {
        return $filter->apply($query);
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
