<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\ProjectRequest;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function printRAB(ProjectRequest $project)
    {
        $rows = $project->rencanaAnggaranBiaya;
        $total = $rows->sum('total');
        $nilaiInvoice = $total * 1.51;
        $margin = $nilaiInvoice - $total;

        return view('print.rab', compact('project', 'rows', 'total', 'nilaiInvoice', 'margin'));
    }

    public function printAssets()
    {
        $assets = Aset::with('template.category', 'lander')->get();

        return view('print.assets', compact('assets'));
    }

    public function printRealisasiRab(ProjectRequest $project)
    {
        $realisasi = $project->realisationRabItems()->with('rabItem')->get();

        return view('print.project-realisasi-rab', [
            'project' => $project,
            'realisasi' => $realisasi,
        ]);
    }
}
