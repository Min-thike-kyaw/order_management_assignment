<?php

namespace App\Models;

use App\Filters\QueryFilter;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes, SecureDeletes;

    protected $fillable = [
        'name', 'name_mm'
    ];


    // Relationships

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    // Scopes
    public function scopeFilter($query, QueryFilter $filter)
    {
        return $filter->apply($query);
    }
}
