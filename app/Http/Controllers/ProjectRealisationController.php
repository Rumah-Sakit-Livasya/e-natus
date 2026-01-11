<?php

namespace App\Http\Controllers;

use App\Models\ProjectRequest;
use App\Models\RencanaAnggaranBiaya;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectRealisationController extends Controller
{
    public function create(ProjectRequest $project): View
    {
        $rabItems = RencanaAnggaranBiaya::where('project_request_id', $project->id)->get();

        return view('project-realisasi.create', [
            'project' => $project,
            'rabItems' => $rabItems,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_request_id' => 'required|exists:project_requests,id',
            'rencana_anggaran_biaya_id' => 'required|exists:rencana_anggaran_biaya,id',
            'description' => 'required|string',
            'qty' => 'required|integer|min:1',
            'harga' => 'required|integer|min:0',
            'tanggal_realisasi' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $data['total'] = $data['qty'] * $data['harga'];

        \App\Models\RealisationRabItem::create($data);

        return redirect()->back()->with('success', 'Realisasi berhasil disimpan.');
    }
}
