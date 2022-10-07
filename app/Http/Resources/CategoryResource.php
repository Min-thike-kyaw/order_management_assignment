<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            "name" => $this->name,
            "name_mm" => $this->name_mm,
            "created_at" => date(config('emart.date_format'),strtotime($this->created_at)),

            "products" => ProductResource::collection($this->whenLoaded('products'))
        ];
    }
}
