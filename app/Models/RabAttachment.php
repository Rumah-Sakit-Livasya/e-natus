<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RabAttachment extends Model
{
    protected $fillable = ['file_path'];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
