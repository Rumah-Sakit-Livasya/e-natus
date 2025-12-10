<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorRental extends Model
{
    protected $fillable = [
        'name',
        'price',
        'qty',
        'unit',
    ];
    //
}
