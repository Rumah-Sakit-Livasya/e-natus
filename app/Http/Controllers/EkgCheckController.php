<?php

namespace App\Http\Controllers;

use App\Models\EkgCheck;

class EkgCheckController extends Controller
{
    public function print(EkgCheck $record)
    {
        $record->load('participant');
        return view('pemeriksaan.ekg', ['record' => $record]);
    }
}
