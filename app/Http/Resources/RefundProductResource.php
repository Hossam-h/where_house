<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RefundProductResource extends JsonResource
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
            'id'          => $this->id,
            'title'       => $this->title_ar,
            'code'        => $this->ean_number,
            'quantity'    => $this->pivot->quantity ?? null,
            'price'       => $this->pivot->price ?? null,
            'cost'        => $this->pivot->cost ?? null,
            'refund_product_id' => $this->pivot->id ?? null,
            'packed_qty'        => $this->pivot->packed_qty ?? null,
            'missing_qty'       => $this->pivot->missing_qty ?? null,
             'images'      => isset($this->images) ? 'https://api-dashboard.morzaq.com/images/products/'.$this->images->pluck('url')[0] : null,
             'unit'        => isset($this->units) &&  count($this->units) > 0? $this->units->pluck('name_'.app()->getLocale())[0] : null
        ];
    }
}
