<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RabClosingFeePetugasItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'price',
        'attachment',
    ];

    public function rabClosing()
    {
        return $this->belongsTo(RabClosing::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(RabAttachment::class, 'attachable');
    }
}
