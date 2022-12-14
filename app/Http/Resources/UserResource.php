<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "name" => $this->name,
            "user_name"=> $this->user_name,
            "email"=> $this->email,
            "created_at"=> date(config('emart.date_format'),strtotime($this->created_at)),
        ];
    }
}
