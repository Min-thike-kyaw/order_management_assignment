<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'name_mm' => $this->name_mm,
            'item_code' => $this->item_code,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'weight' => $this->weight,
            'image' => $this->image,
            'created_at' => date(config('emart.date_format'), strtotime($this->created_at)),
            'category' => new CategoryResource($this->whenLoaded('category'))
        ];
    }

}
