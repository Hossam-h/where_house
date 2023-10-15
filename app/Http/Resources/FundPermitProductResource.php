<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FundPermitProductResource extends JsonResource
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

            'id'          => $this->product->id,
            'title'       => $this->product->description_ar,
            'category_id' => $this->product->category_id,
            'code'        => $this->product->ean_number,
            'quantity'    => $this->quantity ?? null,
            'price'       => $this->price ?? null,
            'cost'        => $this->cost ?? null,
            //'fund_permit_product_id' => $this->id ?? null,
            'packed_qty'        => $this->packed_qty ?? null,
            'missing_qty'       => $this->missing_qty ?? null,
            'images'            => $this->product->images ? 'https://api-dashboard.morzaq.com/images/products/'.$this->product->images->pluck('url')[0] : null,
            'unit'              => $this->product->units  ? $this->product->units->pluck('name_ar')[0] : null
            
        ];
    }
}
