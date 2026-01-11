<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;

    protected $table = 'regions';
    protected $guarded = ['id'];

    public function client()
    {
        return $this->hasMany(Client::class);
    }
}
