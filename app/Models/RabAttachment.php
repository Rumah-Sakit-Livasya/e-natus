<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RabAttachment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
