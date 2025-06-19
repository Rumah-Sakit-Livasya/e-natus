<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SDM extends Model
{
    use SoftDeletes;

    protected $table = 'sdm';
    protected $guarded = ['id'];

    public function projects()
    {
        return $this->hasMany(ProjectRequest::class);
    }
}
