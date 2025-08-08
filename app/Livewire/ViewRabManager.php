<?php

namespace App\Livewire;

// Ganti namespace jika perlu, misal: App\Filament\Widgets
// Hapus use statement yang tidak perlu seperti RealisationRabItem

use App\Models\ProjectRequest;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

// Implementasikan HasForms dan HasActions
class ViewRabManager extends Component implements HasForms, HasActions
{
    // Gunakan trait untuk keduanya
    use InteractsWithForms;
    use InteractsWithActions;

    // Terima record ProjectRequest dari parent
    public ProjectRequest $project;

    // --- DEKLARASIKAN PROPERTI PUBLIK UNTUK VIEW ---
    // Properti ini akan otomatis tersedia sebagai variabel di file Blade.
    public Collection $rabAwalItems;
    public Collection $rabClosingOperasionalItems;
    public Collection $rabClosingFeeItems;
    public float $totalRabAwal = 0;
    public float $totalRabClosing = 0;
    public float $selisih = 0;

    /**
     * Method mount() dieksekusi saat komponen pertama kali dimuat.
     * Ini adalah "konstruktor" untuk komponen.
     */
    public function mount(ProjectRequest $project): void
    {
        $this->project = $project;

        // Panggil method untuk mengisi data agar mount() tetap bersih
        $this->loadData();
    }

    /**
     * Method helper untuk mengambil dan menghitung semua data.
     */
    public function loadData(): void
    {
        // Pastikan relasi sudah di-load untuk menghindari N+1 query problem
        $this->project->loadMissing([
            'rencanaAnggaranBiaya',
            'rabClosing.operasionalItems',
            'rabClosing.feePetugasItems'
        ]);

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
        $this->totalRabAwal = $this->rabAwalItems->sum('total');

        if ($this->project->rabClosing) {
            $this->totalRabClosing = $this->project->rabClosing->total_anggaran_closing;
        }

        $this->selisih = $this->totalRabClosing - $this->totalRabAwal;
    }

    /**
     * Aksi tidak lagi dibutuhkan di sini karena perbandingan adalah tampilan statis.
     * Anda bisa menambahkan tombol print di sini jika mau.
     */
    public function getActions(): array
    {
        // Kosongkan atau tambahkan tombol print jika perlu
        return [];
    }

    /**
     * Method render() yang akan menampilkan file Blade.
     * Karena properti sudah publik, kita tidak perlu mengirimnya secara manual.
     * Livewire akan otomatis menyediakannya untuk view.
     */
    public function render(): View
    {
        return view('livewire.view-rab-manager');
    }
}
