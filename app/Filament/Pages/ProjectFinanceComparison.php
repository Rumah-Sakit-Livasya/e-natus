<?php

namespace App\Filament\Pages;

use App\Models\ProjectRequest;
use Filament\Pages\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProjectFinanceComparison extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static string $view = 'filament.pages.project-finance-comparison';

    protected static bool $shouldRegisterNavigation = false;

    // --- PERUBAHAN UTAMA: DEKLARASIKAN PROPERTI PUBLIK ---
    // Properti ini akan secara otomatis tersedia sebagai variabel di file Blade TABS Anda.
    public ?ProjectRequest $project;
    public Collection $rabAwalItems;
    public Collection $rabClosingOperasionalItems;
    public Collection $rabClosingFeeItems;
    public float $totalRabAwal = 0;
    public float $totalRabClosing = 0;
    public float $selisih = 0;

    /**
     * Method mount() dieksekusi saat halaman pertama kali dimuat.
     */
    public function mount(Request $request): void
    {
        $projectId = $request->query('project');
        if (!$projectId) {
            abort(404, 'Project not found.');
        }

        // Ambil data project beserta semua relasi yang dibutuhkan
        $this->project = ProjectRequest::with([
            'rencanaAnggaranBiaya',
            'rabClosing.operasionalItems',
            'rabClosing.feePetugasItems'
        ])->find($projectId);

        if (!$this->project) {
            abort(404, 'Project not found.');
        }

        // --- PERUBAHAN UTAMA: ISI PROPERTI PUBLIK ---
        // Isi properti dengan data yang sudah diambil.
        $this->rabAwalItems = $this->project->rencanaAnggaranBiaya;

        if ($this->project->rabClosing) {
            $this->rabClosingOperasionalItems = $this->project->rabClosing->operasionalItems;
            $this->rabClosingFeeItems = $this->project->rabClosing->feePetugasItems;
        } else {
            // Jika tidak ada RAB Closing, pastikan koleksinya kosong agar tidak error
            $this->rabClosingOperasionalItems = collect();
            $this->rabClosingFeeItems = collect();
        }

        // Lakukan kalkulasi dan isi properti total
        $this->calculateTotals();
    }

    /**
     * Method helper untuk menghitung semua total dan mengisi properti.
     */
    protected function calculateTotals(): void
    {
        // Hitung total RAB Awal dari koleksi yang sudah di-load
        $this->totalRabAwal = $this->rabAwalItems->sum('total');

        if ($this->project->rabClosing) {
            // Ambil total closing langsung dari recordnya untuk efisiensi
            $this->totalRabClosing = $this->project->rabClosing->total_anggaran_closing;
        }

        // Hitung selisih antara Closing dan Awal
        $this->selisih = $this->totalRabClosing - $this->totalRabAwal;
    }

    /**
     * Mengatur judul halaman.
     */
    public function getTitle(): string
    {
        return 'Perbandingan Anggaran: ' . ($this->project->name ?? '...');
    }
}
