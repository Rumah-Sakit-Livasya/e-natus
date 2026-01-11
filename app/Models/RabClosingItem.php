<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RabClosingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rab_closing_id',
        'description',
        'qty',
        'satuan',
        'harga_satuan',
        'total_anggaran',
    ];

    public function rabClosing(): BelongsTo
    {
        return $this->belongsTo(RabClosing::class);
    }
}
