<?php

namespace App\Http\Controllers;

use App\Models\DrugTest;
use Illuminate\Http\Request;

class DrugTestController extends Controller
{
    public function print(DrugTest $record)
    {
        $record->load('participant');
        return view('pemeriksaan.drug-test', ['record' => $record]);
    }
}
