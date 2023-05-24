<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $guarded = [];


     public function delivery()
    {
        return $this->belongsTo(Delivery::class,'delivery_id');
    }

    public function packingUser()
    {
        return $this->belongsTo(PackingUser::class,'packed_user_id');
    }

    public function refunProducts(){
        return $this->belongsToMany(Product::class,'refund_products')->withPivot('id','quantity','cost','price','packed_qty','missing_qty');
    }

    public function products(){
        return $this->belongsToMany(Product::class,'refund_products')->withPivot('id','quantity','cost','price','packed_qty','missing_qty');
    }

}
