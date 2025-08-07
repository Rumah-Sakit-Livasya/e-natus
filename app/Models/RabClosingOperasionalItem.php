<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabClosingOperasionalItem extends Model
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
}
