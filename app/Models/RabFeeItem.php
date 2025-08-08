<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabFeeItem extends Model
{
    protected $fillable = ['description', 'qty_aset', 'harga_sewa', 'total'];
}
