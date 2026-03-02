<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BmhpOfficeUsage extends Model
{
    protected $table = 'bmhp_office_usages';

    protected $fillable = [
        'bmhp_id',
        'qty_used',
        'used_at',
        'location',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'used_at' => 'date',
    ];

    public function bmhp(): BelongsTo
    {
        return $this->belongsTo(Bmhp::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
