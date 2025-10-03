<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RabClosing extends Model
{
    use HasFactory;

    // Tambahkan semua kolom baru ke $fillable
    protected $fillable = [
        'project_request_id',
        'closing_date',
        'status',
        'total_anggaran', // Ini dari RAB awal
        'total_realisasi',
        'total_anggaran_closing',
        'selisih',
        'keterangan',
        'jumlah_peserta_awal',
        'jumlah_peserta_akhir',
        'nilai_invoice_closing',
        'margin_closing',
        'dana_operasional_transfer',
        'pengeluaran_operasional_closing',
        'sisa_dana_operasional',
        'justifikasi',
    ];

    public function projectRequest(): BelongsTo
    {
        return $this->belongsTo(ProjectRequest::class);
    }

    // Hapus relasi 'items' yang lama
    // public function items(): HasMany
    // {
    //     return $this->hasMany(RabClosingItem::class);
    // }

    // Tambahkan dua relasi baru
    public function operasionalItems(): HasMany
    {
        return $this->hasMany(RabClosingOperasionalItem::class);
    }

    public function feePetugasItems(): HasMany
    {
        return $this->hasMany(RabClosingFeePetugasItem::class);
    }

    public function bmhpItems(): HasMany
    {
        return $this->hasMany(RabClosingBmhpItem::class);
    }
}
