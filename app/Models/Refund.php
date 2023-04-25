<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $guarded = [];



    public function products()
    {
        return $this->belongsToMany(Product::class, RefundProduct::class);
    }
 

     public function deliveries()
    {
        return $this->belongsTo(Delivery::class,'delivery_id');
    }

}
