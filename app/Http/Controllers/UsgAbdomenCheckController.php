<?php

namespace App\Http\Controllers;

use App\Models\UsgAbdomenCheck;

class UsgAbdomenCheckController extends Controller
{
    public function print(UsgAbdomenCheck $record)
    {
        $record->load('participant');
        return view('pemeriksaan.usg-abdomen', ['record' => $record]);
    }
}
