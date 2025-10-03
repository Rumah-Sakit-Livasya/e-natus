<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BmhpStockOpname extends Model
{
    protected $table = 'bmhp_stock_opnames';
    protected $fillable = ['bmhp_id', 'stok_fisik', 'keterangan'];

    public function bmhp()
    {
        return $this->belongsTo(Bmhp::class);
    }
}
