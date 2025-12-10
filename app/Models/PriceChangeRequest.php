<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceChangeRequest extends Model
{
    protected $fillable = [
        'rab_operasional_item_id',
        'requested_by',
        'current_price',
        'requested_price',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'current_price' => 'decimal:2',
        'requested_price' => 'decimal:2',
    ];

    public function rabOperasionalItem()
    {
        return $this->belongsTo(RabOperasionalItem::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
