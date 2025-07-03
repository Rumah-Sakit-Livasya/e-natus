<div>
    {{-- Bagian Header dengan Tombol Aksi --}}
    <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
        {{-- ... Tombol-tombol cetak Anda ... --}}
        <a href="{{ route('print-realisasi-rab', $project) }}" target="_blank" class="filament-button ...">
            <span>🖨️ Cetak Realisasi</span>
        </a>
        <a href="{{ route('print-rab', $project) }}" target="_blank" class="filament-button ...">
            <span>🖨️ Cetak RAB</span>
        </a>

        {{-- Tombol untuk memunculkan form --}}
        <x-filament::button color="success" icon="heroicon-o-plus-circle" size="sm" wire:click="toggleCreateForm">
            {{ $showCreateForm ? 'Batal Tambah' : 'Tambah Realisasi' }}
        </x-filament::button>
    </div>

    <hr class="mb-4">

    {{-- Form yang muncul/hilang --}}
    @if ($showCreateForm)
        <div class="p-4 my-4 border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-800">
            <h3 class="text-lg font-medium mb-4">Form Tambah Realisasi RAB</h3>
            <form wire:submit.prevent="saveRealisasi">
                {{ $this->form }}
                <div class="mt-4">
                    <x-filament::button type="submit" color="primary" class="mt-3">
                        Simpan Realisasi
                    </x-filament::button>
                </div>
            </form>
        </div>
        <hr class="mb-4">
    @endif

    {{--
    PINDAHKAN @include ANDA KE SINI.
    Variabel $project, $rows, $total, dll. tersedia di sini karena
    dikirim dari metode render() di komponen ViewRabManager.php.
    --}}
    @include('components.project-request.view-rab-table', [
        'project' => $project,
        'rows' => $rows,
        'total' => $total,
        'nilaiInvoice' => $nilaiInvoice,
        'margin' => $margin,
    ])

</div>
