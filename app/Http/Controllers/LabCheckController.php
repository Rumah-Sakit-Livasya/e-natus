<?php

namespace App\Http\Controllers;

use App\Models\LabCheck;

class LabCheckController extends Controller
{
    public function print(LabCheck $record)
    {
        $record->load('participant');
        return view('pemeriksaan.lab-check', ['record' => $record]);
    }
}
