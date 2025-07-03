<?php

namespace App\Filament\Pages;

use App\Models\ProjectRequest;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed; // <-- Import Atribut Computed
use Livewire\Attributes\Url;

class ProjectFinanceComparison extends Page
{
    protected static ?string $navigationGroup = 'Project';
    protected static ?string $navigationLabel = 'Perbandingan RAB';
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static string $view = 'filament.pages.project-finance-comparison';

    #[Url(as: 'project', keep: true)]
    public ?int $projectId = null;

    public ?ProjectRequest $project = null;

    // Hapus properti-properti ini, kita akan menggantinya dengan Computed Properties
    // public Collection $comparisonData;
    // public float $totalAwal = 0;
    // public float $totalClosing = 0;
    // public float $totalRealisasi = 0;

    public function mount(): void
    {
        // Mount sekarang hanya bertugas memuat model utama.
        if ($this->projectId) {
            $this->project = ProjectRequest::with([
                'rencanaAnggaranBiaya',
                'realisationRabItems',
                'rabClosing.items'
            ])->find($this->projectId);
        }
    }

    public function getTitle(): string | Htmlable
    {
        return 'Bandingkan Anggaran Proyek';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return $this->project?->name;
    }

    //==============================================================
    // COMPUTED PROPERTIES: Logika utama dan kalkulasi ada di sini
    //==============================================================

    /**
     * Data perbandingan utama.
     * Menggunakan #[Computed] akan meng-cache hasil dari fungsi ini,
     * sehingga tidak dihitung ulang pada setiap render kecuali dependensinya berubah.
     */
    #[Computed(persist: true)]
    public function comparisonData(): Collection
    {
        if (!$this->project) {
            return collect();
        }

        $rabAwal = $this->project->rencanaAnggaranBiaya;
        $rabClosingItems = $this->project->rabClosing?->items;
        $realisasiItems = $this->project->realisationRabItems;

        $allDescriptions = collect()
            ->merge($rabAwal->pluck('description'))
            ->merge($rabClosingItems ? $rabClosingItems->pluck('description') : [])
            ->merge($realisasiItems->pluck('description'))
            ->unique()->values();

        return $allDescriptions->map(function ($description) use ($rabAwal, $rabClosingItems, $realisasiItems) {
            $awalItem = $rabAwal->firstWhere('description', $description);
            $closingItem = $rabClosingItems ? $rabClosingItems->firstWhere('description', $description) : null;
            $realisasiTotal = $realisasiItems->where('description', $description)->sum('total');

            return [
                'description'     => $description,
                'awal_total'      => $awalItem?->total ?? 0,
                'closing_total'   => $closingItem?->total_anggaran ?? 0,
                'realisasi_total' => $realisasiTotal,
            ];
        });
    }

    #[Computed]
    public function totalAwal(): float
    {
        return $this->comparisonData()->sum('awal_total');
    }

    #[Computed]
    public function totalClosing(): float
    {
        return $this->comparisonData()->sum('closing_total');
    }

    #[Computed]
    public function totalRealisasi(): float
    {
        return $this->comparisonData()->sum('realisasi_total');
    }

    #[Computed]
    public function selisihVsAwal(): float
    {
        return $this->totalAwal() - $this->totalRealisasi();
    }

    #[Computed]
    public function selisihFinal(): float
    {
        return $this->totalClosing() - $this->totalRealisasi();
    }

    // Helper untuk deskripsi dan warna di stat card
    #[Computed]
    public function selisihVsAwalDescription(): string
    {
        return $this->selisihVsAwal() >= 0 ? __('project.under_budget') : __('project.over_budget');
    }

    #[Computed]
    public function selisihVsAwalIcon(): string
    {
        return $this->selisihVsAwal() >= 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up';
    }

    #[Computed]
    public function selisihVsAwalColor(): string
    {
        return $this->selisihVsAwal() >= 0 ? 'success' : 'danger';
    }

    #[Computed]
    public function selisihFinalDescription(): string
    {
        $status = $this->selisihFinal() >= 0 ? __('project.profit_label') : __('project.loss_label');
        $formattedValue = 'Rp ' . number_format(abs($this->selisihFinal()), 0, ',', '.');
        return "{$status} {$formattedValue}";
    }

    #[Computed]
    public function selisihFinalColor(): string
    {
        // Kita berikan nama class CSS langsung untuk kemudahan di Blade
        return $this->selisihFinal() >= 0
            ? 'text-success-600 dark:text-success-400'
            : 'text-danger-600 dark:text-danger-400';
    }
}
