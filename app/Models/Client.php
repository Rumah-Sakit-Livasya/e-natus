<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    protected $table = 'clients';
    protected $guarded = ['id'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
