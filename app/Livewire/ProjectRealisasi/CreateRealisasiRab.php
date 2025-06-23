<?php

namespace App\Livewire\ProjectRealisasi;

use App\Models\ProjectRequest;
use App\Models\RencanaAnggaranBiaya;
use App\Models\RealisationRabItem;
use Livewire\Component;

class CreateRealisasiRab extends Component
{
    public ProjectRequest $project;
    public $rabItems = [];

    public $rencana_anggaran_biaya_id;
    public $description;
    public $qty;
    public $harga;
    public $tanggal_realisasi;
    public $keterangan;

    public $showModal = false;

    protected $rules = [
        'rencana_anggaran_biaya_id' => 'required|exists:rencana_anggaran_biaya,id',
        'description' => 'required|string',
        'qty' => 'required|integer|min:1',
        'harga' => 'required|integer|min:0',
        'tanggal_realisasi' => 'required|date',
        'keterangan' => 'nullable|string',
    ];

    public function mount(ProjectRequest $project)
    {
        $this->project = $project;
        $this->rabItems = RencanaAnggaranBiaya::where('project_request_id', $project->id)->get();
    }

    public function save()
    {
        $this->validate();

        RealisationRabItem::create([
            'project_request_id' => $this->project->id,
            'rencana_anggaran_biaya_id' => $this->rencana_anggaran_biaya_id,
            'description' => $this->description,
            'qty' => $this->qty,
            'harga' => $this->harga,
            'total' => $this->harga,
            'tanggal_realisasi' => $this->tanggal_realisasi,
            'keterangan' => $this->keterangan,
        ]);

        $this->reset(['rencana_anggaran_biaya_id', 'description', 'qty', 'harga', 'tanggal_realisasi', 'keterangan', 'showModal']);
        $this->dispatch('realisasiSaved');
    }

    public function render()
    {
        return view('livewire.project-realisasi.create-realisasi-rab');
    }

    protected $listeners = ['showRealisasiModal' => 'open'];

    public function open()
    {
        $this->showModal = true;
    }
}
