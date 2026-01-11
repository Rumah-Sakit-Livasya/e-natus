<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcurementItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_barang',
        'unit',
        'harga_pengajuan',
        'qty_pengajuan',
        'satuan',
        'jumlah',
        'status',
    ];

    protected $table = 'procurement_items';

    public function procurement()
    {
        return $this->belongsTo(Procurement::class);
    }

    public function realisation()
    {
        return $this->hasOne(Realisation::class);
    }
}
