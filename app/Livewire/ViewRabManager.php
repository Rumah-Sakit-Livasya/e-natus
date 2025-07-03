<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProjectRequest;
use App\Models\RealisationRabItem;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Support\RawJs;
use Illuminate\Contracts\View\View;

class ViewRabManager extends Component implements HasForms
{
    use InteractsWithForms;

    public ProjectRequest $project;
    public bool $showCreateForm = false;

    // Properti untuk menampung data form
    public $rencana_anggaran_biaya_id;
    public $description;
    public $qty;
    public $harga;
    public $tanggal_realisasi;
    public $keterangan;

    public function mount(ProjectRequest $project)
    {
        $this->project = $project;
        // Inisialisasi data form di sini jika diperlukan
        $this->form->fill();
    }

    // Definisikan skema form di sini
    protected function getFormSchema(): array
    {
        return [
            \Filament\Forms\Components\Select::make('rencana_anggaran_biaya_id')
                ->label('Item RAB')
                ->options(
                    $this->project->rencanaAnggaranBiaya()
                        ->pluck('description', 'id')
                        ->toArray()
                )
                ->searchable()
                ->required(),
            \Filament\Forms\Components\TextInput::make('description')->label('Deskripsi')->required(),
            \Filament\Forms\Components\TextInput::make('qty')->label('Jumlah')->numeric()->required(),
            \Filament\Forms\Components\TextInput::make('harga')->label('Harga')->mask(RawJs::make('$money($input)'))
                ->dehydrateStateUsing(function (?string $state): ?string {
                    if ($state === null) {
                        return null;
                    }

                    $cleanedState = preg_replace('/[^\d]/', '', $state);

                    return $cleanedState;
                })->required(),
            \Filament\Forms\Components\DatePicker::make('tanggal_realisasi')->label('Tanggal Realisasi')->default(now())->required(),
            \Filament\Forms\Components\Textarea::make('keterangan')->label('Keterangan')->rows(2)->nullable(),
        ];
    }

    // Aksi untuk menyimpan data
    public function saveRealisasi()
    {
        $data = $this->form->getState();

        RealisationRabItem::create([
            'project_request_id' => $this->project->id,
            'rencana_anggaran_biaya_id' => $data['rencana_anggaran_biaya_id'],
            'description' => $data['description'],
            'qty' => $data['qty'],
            'harga' => $data['harga'],
            'total' => $data['qty'] * $data['harga'],
            'tanggal_realisasi' => $data['tanggal_realisasi'],
            'keterangan' => $data['keterangan'] ?? null,
            // Status bisa di-handle di sini juga
            'status' => 'draft',
        ]);

        Notification::make()
            ->title('Realisasi berhasil ditambahkan')
            ->success()
            ->send();

        // Reset form dan sembunyikan
        $this->form->fill();
        $this->showCreateForm = false;

        // Emit event untuk menutup modal jika diinginkan
        // $this->dispatchBrowserEvent('close-modal', ['id' => 'view-rab-modal']);
    }

    // Toggle untuk menampilkan/menyembunyikan form
    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
    }

    public function render(): View
    {
        $rows = $this->project->rencanaAnggaranBiaya;
        $total = $rows->sum('total');
        $nilaiInvoice = $this->project->nilai_invoice;
        $margin = $nilaiInvoice - $total;

        return view('livewire.view-rab-manager', [
            'rows' => $rows,
            'total' => $total,
            'nilaiInvoice' => $nilaiInvoice,
            'margin' => $margin,
        ]);
    }
}
