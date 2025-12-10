<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabOperasionalItem extends Model
{
    protected $fillable = ['description', 'qty_aset', 'harga_sewa', 'total', 'is_vendor_rental'];

    protected $casts = [
        'is_vendor_rental' => 'boolean',
    ];

    public function priceChangeRequests()
    {
        return $this->hasMany(\App\Models\PriceChangeRequest::class);
    }
}
