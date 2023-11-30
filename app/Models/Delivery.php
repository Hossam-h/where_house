<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, Relations\HasMany};

class Delivery extends Model
{

    use HasFactory;
    public function refunds(){
        return $this->hasMany(Refund::class,'delivery_id')->where('status','cancelled');
    }

    public function refundsPartial(){
        return $this->hasMany(Refund::class,'delivery_id')->where('status','partial');
    }
    
    public function oldRefund(){

    }
}
