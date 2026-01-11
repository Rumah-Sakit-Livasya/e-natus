<?php

namespace App\Http\Controllers;

use App\Models\SpirometryCheck;

class SpirometryCheckController extends Controller
{
    public function print(SpirometryCheck $record)
    {
        $record->load('participant');
        return view('pemeriksaan.spirometri', ['record' => $record]);
    }
}
