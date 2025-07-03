<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RabComparisonStats;
use App\Models\ProjectRequest;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View; // Impor ini untuk type-hinting yang lebih baik
use Illuminate\Support\Facades\Request;

class CompareRab extends Page
{
    /**
     * Tentukan ikon navigasi untuk halaman ini.
     */
    protected static ?string $navigationIcon = 'heroicon-o-scale';

    /**
     * Tentukan file Blade yang akan digunakan untuk merender halaman ini.
     */
    protected static string $view = 'filament.pages.compare-rab';

    /**
     * Tentukan slug URL untuk halaman ini.
     * Contoh: /dashboard/compare-rab
     */
    protected static ?string $slug = 'compare-rab';

    /**
     * Kita tidak ingin halaman ini muncul otomatis di menu navigasi utama.
     * Halaman ini hanya akan diakses melalui tombol dari resource lain.
     */
    protected static bool $shouldRegisterNavigation = false;

    /**
     * Properti publik untuk menyimpan data yang akan digunakan di view.
     */
    public ProjectRequest $record;
    public $rabClosing;
    public $realisationsGrouped;

    /**
     * Metode mount() dieksekusi saat halaman pertama kali dimuat.
     * Ini adalah tempat untuk mengambil dan menyiapkan semua data.
     */
    public function mount(): void
    {
        // 1. Ambil ID record proyek dari parameter URL (?record=...)
        $recordId = Request::query('record');
        if (!$recordId) {
            abort(404, 'ID Proyek tidak ditemukan.');
        }

        // 2. Ambil data ProjectRequest dari database. `findOrFail` akan otomatis 404 jika tidak ada.
        $this->record = ProjectRequest::findOrFail($recordId);

        // 3. Ambil data RAB Closing yang terhubung dengan proyek ini, beserta item-itemnya.
        //    Gunakan `with('items')` untuk Eager Loading, ini lebih efisien.
        $this->rabClosing = $this->record->rabClosing()->with('items')->first();

        // 4. Jika tidak ada RAB Closing, halaman ini tidak bisa ditampilkan.
        if (!$this->rabClosing) {
            abort(404, 'RAB Closing untuk proyek ini belum dibuat.');
        }

        // 5. Ambil semua data realisasi untuk proyek ini.
        //    Kelompokkan berdasarkan 'description' dan jumlahkan totalnya.
        //    Ini agar kita bisa memetakan realisasi ke setiap item anggaran.
        $this->realisationsGrouped = $this->record->realisationRabItems()
            ->selectRaw('description, SUM(total) as total_realisasi, GROUP_CONCAT(keterangan SEPARATOR ", ") as keterangan_realisasi')
            ->groupBy('description')
            ->get()
            ->keyBy('description'); // `keyBy` membuat 'description' menjadi kunci array, mudah dicari.
    }

    /**
     * Menentukan judul halaman yang akan ditampilkan di header.
     */
    public function getTitle(): string
    {
        return 'Perbandingan RAB: ' . $this->record->name;
    }

    /**
     * Mendaftarkan widget yang akan ditampilkan di bagian atas halaman ini.
     */
    public function getHeaderWidgets(): array
    {
        return [
            // Panggil widget statistik dan kirim data record proyek ke dalamnya
            RabComparisonStats::make(['record' => $this->record]),
        ];
    }
}
