<?php

namespace App\Http\Controllers;

use App\Models\TreadmillCheck;

class TreadmillCheckController extends Controller
{
    public function print(TreadmillCheck $record)
    {
        $record->load('participant');
        return view('pemeriksaan.treadmill', ['record' => $record]);
    }
}
