<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, Relations\HasMany};

class Delivery extends Model
{
    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function order_invoices() : HasMany
    {
        return $this->hasMany(OrderInvoice::class, 'delivery_id', 'id');
    }
    public function refunds() : HasMany
    {
        return $this->hasMany(Refund::class, 'delivery_id', 'id');
    }
}
