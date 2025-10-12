<?php

namespace App\Http\Controllers;

use App\Models\AudiometryCheck;
use Illuminate\Http\Request;

class AudiometryCheckController extends Controller
{
    /**
     * Menampilkan halaman cetak untuk hasil pemeriksaan audiometri.
     */
    public function print(AudiometryCheck $record)
    {
        // Eager load relasi 'participant' untuk efisiensi
        $record->load('participant');

        // Kirim data ke view
        return view('pemeriksaan.audiometry', ['record' => $record]);
    }
}
