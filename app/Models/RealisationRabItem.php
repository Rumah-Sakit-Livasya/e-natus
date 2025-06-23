<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealisationRabItem extends Model
{
    protected $fillable = [
        'project_request_id',
        'rencana_anggaran_biaya_id',
        'description',
        'qty',
        'harga',
        'total',
        'keterangan',
        'tanggal_realisasi',
    ];

    public function project()
    {
        return $this->belongsTo(ProjectRequest::class, 'project_request_id');
    }

    public function rabItem()
    {
        return $this->belongsTo(RencanaAnggaranBiaya::class, 'rencana_anggaran_biaya_id');
    }
}
