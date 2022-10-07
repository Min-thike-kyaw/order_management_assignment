<?php

namespace App\Filters;


class CustomerFilter extends QueryFilter
{
    public function name($value = '')
    {
        return $this->builder->where('name', 'like', "%$value%");
    }

    public function phone_no($value = '')
    {
        return $this->builder->where('phone_no', 'like', "%$value%");
    }

    public function city_name($value = '')
    {
        return $this->builder->where('city_name', 'like', "%$value%");
    }

    public function zone_name($value = '')
    {
        return $this->builder->where('zone_name', 'like', "%$value%");
    }
}
