<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RabClosingBmhpItem extends Model
{
    use HasFactory;

    protected $table = 'rab_closing_bmhp_items';

    protected $fillable = [
        'rab_closing_id',
        'bmhp_id',
        'name',          // Nama BMHP
        'satuan',
        'jumlah_rencana',
        'harga_satuan',
        'total',
    ];

    public function rabClosing(): BelongsTo
    {
        return $this->belongsTo(RabClosing::class);
    }

    public function bmhp(): BelongsTo
    {
        return $this->belongsTo(Bmhp::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(RabAttachment::class, 'attachable');
    }
}
