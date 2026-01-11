<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class McuAttachment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function mcuResult(): BelongsTo
    {
        return $this->belongsTo(McuResult::class);
    }
}
