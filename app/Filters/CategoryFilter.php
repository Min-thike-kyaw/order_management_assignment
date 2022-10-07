<?php

namespace App\Filters;


class CategoryFilter extends QueryFilter
{
    public function name($value = '')
    {
        return $this->builder->where('name', 'like', "%$value%");
    }
    public function name_mm($value = '')
    {
        return $this->builder->where('name_mm', 'like', "%$value%");
    }
}
