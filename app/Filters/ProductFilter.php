<?php

namespace App\Filters;


class ProductFilter extends QueryFilter
{
    public function name($value = '')
    {
        return $this->builder->where('name', 'like', "%$value%");
    }

    public function name_mm($value = '')
    {
        return $this->builder->where('name_mm', 'like', "%$value%");
    }

    public function category($value = '')
    {
        return $this->builder->whereHas('category',function($query) use ($value) {
            $query->where('name_mm', 'like', "%$value%")
                  ->orWhere('name_mm', 'like' , "%$value%");
        });
    }
    public function item_code($value = '')
    {
        return $this->builder->where('item_code', 'like', "%$value%");
    }
}
