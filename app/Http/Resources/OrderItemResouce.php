<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResouce extends JsonResource
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
            "id" => $this->id,
            "order_id" => $this->order_id,
            "product_id" => $this->product_id,
            "qty" => $this->qty,
            "price" => $this->price,
            "unit_price" => $this->unit_price,
            "amount" => $this->amount,
            "created_at" => date(config('emart.date_format'), strtotime($this->created_at)),

            "order" => new OrderResource($this->whenLoaded('order')),
            "product" => new ProductResource($this->whenLoaded('product'))
        ];
    }
}
