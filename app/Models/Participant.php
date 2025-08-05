<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = ['date_of_birth' => 'date'];

    public function mcuResults(): HasMany
    {
        return $this->hasMany(McuResult::class);
    }

    /**
     * Seorang Participant milik sebuah Project Request.
     */
    public function projectRequest(): BelongsTo
    {
        return $this->belongsTo(ProjectRequest::class);
    }
}
