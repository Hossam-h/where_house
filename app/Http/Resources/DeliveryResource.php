<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
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
            'cost'                 => $this->name_ar,
            'packed_start_time'    => $this->code ?? null,
            'partial_refund'       => RefundResource::collection($this->refundsPartial),
            'approve_refund'       => RefundResource::collection($this->refunds)
        ];
    }
}
