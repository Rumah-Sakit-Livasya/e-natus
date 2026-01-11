<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectBmhp extends Model
{
    use SoftDeletes;

    protected $table = 'project_bmhp';

    protected $fillable = [
        'bmhp_id',
        'satuan',
        'jumlah_rencana',
        'harga_satuan',
        'total',
        'project_request_id',
    ];

    public function projectRequest()
    {
        return $this->belongsTo(ProjectRequest::class);
    }

    public function bmhp()
    {
        return $this->belongsTo(Bmhp::class, 'bmhp_id');
    }
}
