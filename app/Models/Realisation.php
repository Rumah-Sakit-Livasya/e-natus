<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Realisation extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $table = 'realisations';

    protected static function booted()
    {
        static::saving(function ($realisation) {
            $hargaPengajuan = $realisation->procurementItem->jumlah_pengajuan ?? 0;
            $hargaRealisasi = $realisation->harga_realisasi ?? 0;

            if ($hargaPengajuan > 0) {
                $realisation->persentase_hemat = round((($hargaPengajuan - $hargaRealisasi) / $hargaPengajuan) * 100, 2);
            } else {
                $realisation->persentase_hemat = 0;
            }
        });
    }

    // Realisation.php (model)
    public function procurementItem()
    {
        return $this->belongsTo(ProcurementItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class);
    }
}
