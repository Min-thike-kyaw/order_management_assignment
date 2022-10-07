<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\OrderRepositoryInterface;

class OrderController extends BaseController
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository) {
        $this->orderRepository = $orderRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponseFromResult($this->orderRepository->getAllOrders());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->only(['customer_id', 'total_discount', 'order_items']);
        $validator = Validator::make($input, [
            'customer_id' => ['required', Rule::exists('customers', 'id')->where(function($query){
                $query->where('is_active', true)->whereNull('deleted_at');
            })],
            'total_discount' => 'required|gte:0',
            'order_items' => 'required|array',
            'order_items.*.product_id' => "required|exists:products,id",
            'order_items.*.qty' => "required|integer",
            'order_items.*.price' => "required|integer",

        ], [
            'order_items.*.product_id.required' => 'Product quantities are required',
            'order_items.*.qty.required' => 'Product quantities are required',
            'order_items.*.price.required' => 'Product price are required',
        ]);


        if($validator->fails()){
            return $this->sendError( $validator->errors());
        }

        return $this->sendResponseFromResult($this->orderRepository->createOrder($input));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendResponseFromResult($this->orderRepository->getOrderById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->only(['customer_id', 'total_discount', 'status']);
        $validator = Validator::make($input, [
            'customer_id' => ['integer', Rule::exists('customers', 'id')->where(function($query){
                $query->where('is_active', true)->whereNull('deleted_at');
            })],
            'total_discount' => 'integer|gte:0',
            'status' => 'boolean'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        return $this->sendResponseFromResult($this->orderRepository->updateOrder($id, $input));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->sendResponseFromResult($this->orderRepository->deleteOrder($id));
    }

    public function addItem(Request $request,$id)
    {
        $input = $request->only(['product_id', 'qty', 'price']);
        $validator = Validator::make($input, [
            'product_id' => ['required','integer', Rule::exists('products', 'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'qty' => 'required|integer|gte:0',
            'price' => 'required|integer|gte:0'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        return $this->sendResponseFromResult($this->orderRepository->addItem($id, $input));
    }

    public function addMultipleItems(Request $request,$id)
    {
        // return $request->all();
        $input = $request->only(['order_items']);

        $validator = Validator::make($input, [
            'order_items' => 'required|array',
            'order_items.*.product_id' => "required|exists:products,id",
            'order_items.*.qty' => "required|integer",
            'order_items.*.price' => "required|integer",

        ], [
            'order_items.*.product_id.required' => 'Product quantities are required',
            'order_items.*.qty.required' => 'Product quantities are required',
            'order_items.*.price.required' => 'Product price are required',
        ]);
        // return type($input);
        if($validator->fails()){
            return $this->sendError( $validator->errors());
        }

        return $this->sendResponseFromResult($this->orderRepository->addMultipleItems($id,$input));
    }

    public function removeItems(Request $request, $id)
    {
        $input = $request->only(['order_item_ids']);
        $validator = Validator::make($input,[
            'order_item_ids' => 'array|required'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        return $this->sendResponseFromResult($this->orderRepository->removeItems($id,$input ));
    }

    public function restore($id)
    {
        return $this->sendResponseFromResult($this->orderRepository->restoreOrder($id));
    }

    public function permanentDelete($id)
    {
        return $this->sendResponseFromResult($this->orderRepository->forceDeleteOrder($id));
    }
}
