<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\OrderItemRepositoryInterface;

class OrderItemController extends BaseController
{

    private OrderItemRepositoryInterface $orderItemRepository;

    public function __construct(OrderItemRepositoryInterface $orderItemRepository) {
        $this->orderItemRepository = $orderItemRepository;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->only(['product_id', 'price', 'qty', 'order_id']);
        $validator = Validator::make($input, [
            'order_id' => "required|exists:orders,id",
            'product_id' => ['required','integer', Rule::exists('products', 'id')->where(function($query){
                $query->where('is_available', true)->whereNull('deleted_at');
            })],
            'qty' => "required|integer",
            'price' => "required|integer",

        ], [
            'product_id.required' => 'Product id is required',
            'qty.required' => 'Product quantity is required',
            'price.required' => 'Product price is required',
        ]);


        if($validator->fails()){
            return $this->sendError( $validator->errors());
        }

        return $this->sendResponseFromResult($this->orderItemRepository->createOrderItem($input));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function show(OrderItem $orderItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderItem $orderItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->only(['product_id', 'qty', 'price']);
        $validator = Validator::make($input, [
            'product_id' => ['integer', Rule::exists('products', 'id')->where(function($query){
                $query->where('is_available', true)->whereNull('deleted_at');
            })],
            'qty' => 'integer|gte:0',
            'price' => 'integer|gte:0'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        return $this->sendResponseFromResult($this->orderItemRepository->updateOrderItem($id, $input));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->sendResponseFromResult($this->orderItemRepository->deleteOrderItem($id));
    }

    public function restore($id)
    {
        return $this->sendResponseFromResult($this->orderItemRepository->restoreOrderItem($id));
    }

    public function permanentDelete($id)
    {
        return $this->sendResponseFromResult($this->orderItemRepository->forceDeleteOrderItem($id));
    }
}
