<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['image'];

    public function refunds(){
        return $this->belongsToMany(Refund::class,'refund_products','product_id')->withPivot('id','quantity','cost','price','packed_qty','missing_qty');
    }
    

    public function fundPermit(){
        return $this->belongsToMany(Refund::class,'fund_permit_products','product_id')->withPivot('id','quantity','cost','price','packed_qty','missing_qty');
    }
    

    public function images()
    {
        return $this->hasMany(Image::class);
    }
   
    public function getImageAttribute()
    {
        return $this->images()->first() ? 'https://api-dashboard.morzaq.com/images/products/'.$this->images()->first()->url : null;
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class,'product_units');
    }

}
