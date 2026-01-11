<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AudiometryCheck extends Model
{
    use HasFactory;

    /**
     * Properti ini penting agar semua field dari form Anda
     * bisa disimpan ke database.
     */
    protected $guarded = ['id'];

    /**
     * =========================================================
     * TAMBAHKAN RELASI INI
     * =========================================================
     * Fungsi ini mendefinisikan bahwa setiap data AudiometryCheck
     * "milik" satu data Participant. Nama fungsi 'participant'
     * harus sama persis dengan yang Anda tulis di ->relationship().
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }
}
