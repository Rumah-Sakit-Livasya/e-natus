<?php

namespace App\Http\Controllers;

use App\Models\Participant;

class ParticipantReportController extends Controller
{
    public function printSummary(Participant $participant)
    {
        // Eager load semua relasi pemeriksaan untuk efisiensi
        $participant->load([
            'audiometryChecks',
            'drugTests',
            'ekgChecks',
            'labChecks',
            'rontgenChecks',
            'spirometryChecks',
            'treadmillChecks',
            'usgAbdomenChecks',
            'usgMammaeChecks',
        ]);

        return view('pemeriksaan.summary-report', ['participant' => $participant]);
    }
}
