<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"=> $this->id,
            "customer_id"=> $this->customer_id,
            "total_qty"=> $this->total_qty,
            "total_amount"=> $this->total_amount,
            "total_discount"=> $this->total_discount,
            "grand_total"=> $this->grand_total,
            "remark"=> $this->remark,
            "status"=> $this->status,
            "order_no"=> $this->order_no,
            "created_at"=> date(config('emart.date_format'), strtotime($this->created_at)),

            "customer" => new CustomerResource($this->whenLoaded('customer')),
            "order_items" => OrderItemResouce::collection($this->whenLoaded('orderItems')),
        ];
    }
}
