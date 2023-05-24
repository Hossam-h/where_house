<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundPermit extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function products(){
        return $this->belongsToMany(Product::class,'fund_permit_products')->withPivot('id','quantity','cost','price','packed_qty','missing_qty');
    }
    public function delivery()
    {
        return $this->belongsTo(Delivery::class,'delivery_id');
    }

    public function packingUser()
    {
        return $this->belongsTo(PackingUser::class,'packed_user_id');
    }

}
