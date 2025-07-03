<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RabClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_request_id',
        'closing_date',
        'total_anggaran',
        'total_realisasi',
        'selisih',
        'keterangan',
    ];

    public function projectRequest(): BelongsTo
    {
        return $this->belongsTo(ProjectRequest::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RabClosingItem::class);
    }
}
