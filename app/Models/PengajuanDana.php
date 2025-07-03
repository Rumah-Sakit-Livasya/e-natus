<?php

namespace App\Models;

use App\Enums\StatusPengajuanEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanDana extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi secara massal
    protected $guarded = [];

    // Cast properti ke tipe data yang benar
    protected $casts = [
        'status' => StatusPengajuanEnum::class,
        // 'jumlah_diajukan' => 'float', // Ganti dari 'decimal:2' menjadi 'float' atau 'double'
        'tanggal_pengajuan' => 'date',
        'approved_at' => 'datetime',
        'dicairkan_at' => 'datetime',
    ];

    // Relasi ke ProjectRequest
    public function projectRequest(): BelongsTo
    {
        return $this->belongsTo(ProjectRequest::class);
    }

    // Relasi ke User yang mengajukan
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke User yang menyetujui
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
