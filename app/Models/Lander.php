<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lander extends Model
{
    use SoftDeletes;
    protected $table = 'landers';
    protected $guarded = ['id'];

    public function asets()
    {
        return $this->hasMany(Aset::class);
    }
}
