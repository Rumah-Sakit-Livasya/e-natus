<?php

namespace App\Livewire;

use App\Models\ProjectRequest;
use App\Models\RealisationRabItem;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\RawJs;
use Illuminate\Contracts\View\View;
use Livewire\Component;

// Implementasikan HasForms dan HasActions
class ViewRabManager extends Component implements HasForms, HasActions
{
    // Gunakan trait untuk keduanya
    use InteractsWithForms;
    use InteractsWithActions;

    public ProjectRequest $project;

    // Properti `$showCreateForm` dan properti form lainnya tidak diperlukan lagi

    public function mount(ProjectRequest $project): void
    {
        $this->project = $project;
    }

    /**
     * Ini adalah method utama untuk mendefinisikan semua tombol ("Actions")
     * yang akan ditampilkan di view.
     */
    public function getActions(): array
    {
        return [
            // Aksi untuk membuka modal tambah realisasi
            $this->createRealisasiAction(),

            // Aksi untuk mencetak
            $this->printRealisasiAction(),
            $this->printRabAction(),
        ];
    }

    // Saya memecah setiap aksi menjadi method sendiri agar lebih rapi

    protected function createRealisasiAction(): Action
    {
        return Action::make('createRealisasi')
            ->label('Tambah Realisasi')
            ->color('success')
            ->icon('heroicon-o-plus-circle')
            ->size('sm')
            // Definisikan form yang akan muncul di dalam modal
            ->form([
                Select::make('rencana_anggaran_biaya_id')
                    ->label('Item RAB Awal')
                    ->options($this->project->rencanaAnggaranBiaya()->pluck('description', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('description')
                    ->label('Deskripsi Realisasi')
                    ->helperText('Isi deskripsi spesifik untuk realisasi ini, misal: "Sewa AC hari pertama".')
                    ->required(),
                TextInput::make('qty')->label('Jumlah')->numeric()->required(),
                TextInput::make('harga')
                    ->label('Harga')
                    ->required()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(','),
                DatePicker::make('tanggal_realisasi')->label('Tanggal Realisasi')->default(now())->required(),
                Textarea::make('keterangan')->label('Keterangan')->rows(2)->nullable(),
            ])
            // Logika yang dijalankan saat tombol "Simpan" di modal diklik
            ->action(function (array $data) {
                // Ambil deskripsi dari item RAB awal sebagai fallback jika field 'description' tidak ada
                $itemRAB = $this->project->rencanaAnggaranBiaya()->find($data['rencana_anggaran_biaya_id']);

                RealisationRabItem::create([
                    'project_request_id' => $this->project->id,
                    'rencana_anggaran_biaya_id' => $data['rencana_anggaran_biaya_id'],
                    'description' => $data['description'] ?? $itemRAB->description, // Gunakan deskripsi dari form
                    'qty' => $data['qty'],
                    'harga' => $data['harga'],
                    'total' => (float) $data['qty'] * (float) $data['harga'],
                    'tanggal_realisasi' => $data['tanggal_realisasi'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'status' => 'draft',
                ]);

                Notification::make()
                    ->title('Realisasi berhasil ditambahkan')
                    ->success()
                    ->send();
            });
    }

    protected function printRealisasiAction(): Action
    {
        return Action::make('printRealisasi')
            ->label('Cetak Realisasi')
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->size('sm')
            ->url(route('print-realisasi-rab', $this->project))
            ->openUrlInNewTab();
    }

    protected function printRabAction(): Action
    {
        return Action::make('printRab')
            ->label('Cetak RAB')
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->size('sm')
            ->url(route('print-rab', $this->project))
            ->openUrlInNewTab();
    }

    // --- PERUBAHAN UTAMA ADA DI SINI ---
    public function render(): View
    {
        // 1. Ambil data untuk Tabel Anggaran (RAB Awal)
        $rabItems = $this->project->rencanaAnggaranBiaya;
        $totalAnggaran = $rabItems->sum('total');

        // 2. Ambil data untuk Tabel Realisasi
        // Pastikan relasi 'realisationRabItems' ada di model ProjectRequest Anda
        $realisasiItems = $this->project->realisationRabItems()->with('rabItem')->get();
        $totalRealisasi = $realisasiItems->sum('total');

        // 3. Hitung data summary lainnya
        $nilaiInvoice = $this->project->nilai_invoice;
        $selisih = $totalAnggaran - $totalRealisasi;
        $margin = $nilaiInvoice - $totalAnggaran;

        // 4. Kirim SEMUA data ke view
        return view('livewire.view-rab-manager', [
            // Data untuk Tabel 1: Anggaran
            'rabItems' => $rabItems,
            'totalAnggaran' => $totalAnggaran,

            // Data untuk Tabel 2: Realisasi
            'realisasiItems' => $realisasiItems,
            'totalRealisasi' => $totalRealisasi,

            // Data Summary
            'nilaiInvoice' => $nilaiInvoice,
            'selisih' => $selisih,
            'margin' => $margin,
        ]);
    }
}
