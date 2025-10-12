<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrugTest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }
}
