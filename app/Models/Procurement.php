<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procurement extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $table = 'procurements';

    protected $casts = [
        'tanggal_pengajuan' => 'date',
    ];

    // App\Models\Procurement.php
    public function items()
    {
        return $this->hasMany(\App\Models\ProcurementItem::class);
    }

    public function asetReceipt()
    {
        return $this->hasOne(AsetReceipt::class);
    }

    public function realisations()
    {
        return $this->hasManyThrough(
            Realisation::class,
            ProcurementItem::class,
            'procurement_id',        // FK di procurement_items
            'procurement_item_id',   // FK di realisations
            'id',                    // PK di procurements
            'id'                     // PK di procurement_items
        );
    }
}
