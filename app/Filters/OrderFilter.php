<?php

namespace App\Filters;


class OrderFilter extends QueryFilter
{

    public function customer_name($value = '')
    {
        return $this->builder->whereHas('customer', function($query) use($value) {
            $query->where('customers.name', 'like', "%$value%");
        });
    }

    public function status($value = '')
    {
        return $this->builder->where('status', $value);
    }

    public function from($value = '')
    {
        return $this->builder->where('created_at' , '>=', $value);
    }

    public function to($value = '')
    {
        return $this->builder->where('created_at', '<=', $value);
    }
}
