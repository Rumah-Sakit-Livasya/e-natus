<?php

namespace App\Http\Controllers;

use App\Models\RabClosing; // <-- Tambahkan ini
use Illuminate\Http\Request;

class RabClosingController extends Controller
{
    /**
     * Menampilkan halaman cetak untuk RAB Closing.
     */
    public function print(RabClosing $record)
    {
        // Muat semua relasi yang dibutuhkan untuk menghindari N+1 query di view
        $record->load([
            'projectRequest.client', // Ambil data klien
            'operasionalItems',      // Ambil semua item operasional
            'feePetugasItems'        // Ambil semua item fee
        ]);

        // Kirim data ke view yang akan kita buat
        return view('print.rab-closing', ['record' => $record]);
    }
}
