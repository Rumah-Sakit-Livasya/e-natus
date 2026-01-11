<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class McuResult extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'tanggal_mcu' => 'date',
        'anamnesa' => 'array',
        'riwayat_penyakit_dan_gaya_hidup' => 'array',
        'hasil_pemeriksaan_vital_sign' => 'array',
        'hasil_pemeriksaan_fisik_dokter' => 'array',
        'hasil_laboratorium' => 'array',
        'hasil_pemeriksaan_penunjang' => 'array',
        'status_kesehatan' => 'array',
        'kesimpulan_dan_saran' => 'array',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function projectRequest(): BelongsTo
    {
        return $this->belongsTo(ProjectRequest::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(McuAttachment::class);
    }
}
