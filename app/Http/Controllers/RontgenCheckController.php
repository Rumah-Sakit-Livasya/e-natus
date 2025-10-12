<?php

namespace App\Http\Controllers;

use App\Models\RontgenCheck;

class RontgenCheckController extends Controller
{
    public function print(RontgenCheck $record)
    {
        $record->load('participant');
        return view('pemeriksaan.rontgen', ['record' => $record]);
    }
}
