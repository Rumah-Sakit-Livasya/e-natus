<?php

namespace App\Http\Controllers;

use App\Models\UsgMammaeCheck;

class UsgMammaeCheckController extends Controller
{
    public function print(UsgMammaeCheck $record)
    {
        $record->load('participant');
        return view('pemeriksaan.usg-mammae', ['record' => $record]);
    }
}
