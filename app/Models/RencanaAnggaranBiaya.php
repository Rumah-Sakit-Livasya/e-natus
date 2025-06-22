<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaAnggaranBiaya extends Model
{
    protected $table = 'rencana_anggaran_biaya';

    protected $fillable = [
        'project_request_id',
        'description',
        'qty_aset',
        'harga_sewa',
        'total',
    ];

    public function project()
    {
        return $this->belongsTo(ProjectRequest::class, 'project_request_id');
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
