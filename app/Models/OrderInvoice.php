<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, Relations\HasMany};

class OrderInvoice extends Model
{
    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function order_product() : HasMany
    {
        return $this->hasMany(OrderProduct::class, 'invoice_id', 'id');
    }
}
