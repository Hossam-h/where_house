<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RefundResource extends JsonResource
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
            'id'                   => $this->id,
            'cost'                 => $this->cost,
            'packed_start_time'    => $this->packed_start_time ?? null,
            'packed_end_time'      => $this->packed_end_time ?? null,
            'created_at'           => date('d M Y', strtotime($this->created_at)),

            'products'       => RefundProductResource::collection($this->products),
            'delivery'       => (object) ["id" => $this->delivery->id ?? null , "name" => $this->delivery->name_ar ?? null],
            'packed_user'    => (object) ["id" => $this->packingUser->id ??null , "name" => $this->packingUser->name_ar??null]
        ];
    }
}
