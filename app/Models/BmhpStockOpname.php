<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BmhpStockOpname extends Model
{
    protected $table = 'bmhp_stock_opnames';
    protected $fillable = ['bmhp_id', 'stok_fisik', 'keterangan'];

    public function bmhp(): BelongsTo
    {
        return $this->belongsTo(Bmhp::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
