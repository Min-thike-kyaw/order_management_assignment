<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    protected $builder;
    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }
    public function apply(Builder $builder)
    {
        $this->builder = $builder;
        foreach($this->filters() as $key => $value) {
            if(method_exists($this, $key)) {
                call_user_func_array([$this,$key], array_filter([$value]));
            }
        }
        return $this->builder;
    }

    public function filters()
    {
        return $this->request->all();
    }
}
