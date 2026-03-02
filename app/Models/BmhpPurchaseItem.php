<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BmhpPurchaseItem extends Model
{
    protected $table = 'bmhp_purchase_items';

    protected $fillable = [
        'bmhp_purchase_id',
        'bmhp_id',
        'purchase_type',
        'qty',
        'pcs_per_unit_snapshot',
        'total_pcs',
        'harga',
        'subtotal',
        'is_checked',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(BmhpPurchase::class, 'bmhp_purchase_id');
    }

    public function bmhp(): BelongsTo
    {
        return $this->belongsTo(Bmhp::class);
    }
}
