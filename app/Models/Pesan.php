<?php
// app/Models/Pesan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'pesan'; // Sesuaikan jika nama tabel Anda berbeda

    /**
     * Nonaktifkan timestamps bawaan Laravel (created_at, updated_at).
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'info_app',
        'nama',
        'nomor',
        'id_pesan',
        'pesan',
        'balasan',
        'status_balasan',
        'message',
        'data',
        'status',
        // Kolom 'tanggal' akan diisi otomatis oleh database
    ];
}
