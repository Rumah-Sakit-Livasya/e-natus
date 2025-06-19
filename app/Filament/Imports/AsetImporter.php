<?php

namespace App\Filament\Imports;

use App\Models\Aset;
use App\Models\Lander;
use App\Models\Template;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AsetImporter extends Importer
{
    protected static ?string $model = Aset::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('template_name')->label('Nama Template'),
            ImportColumn::make('lander_name')->label('Nama Lander'),
            ImportColumn::make('custom_name')->label('Nama Aset'),
            ImportColumn::make('condition')->label('Kondisi'),
            ImportColumn::make('brand')->label('Merk'),
            ImportColumn::make('purchase_year')->label('Tahun Pembelian'),
            ImportColumn::make('tarif')->label('Tarif'),
            ImportColumn::make('satuan')->label('Satuan'),
            ImportColumn::make('index')->label('Index'),
            ImportColumn::make('status')->label('Status'),
        ];
    }

    public function resolveRecord(): ?Aset
    {
        $template = Template::with('category')->where('name', $this->data['template_name'])->first();
        $lander = Lander::where('name', $this->data['lander_name'])->first();

        if (! $template || ! $lander) {
            return null;
        }

        // Validasi index, pakai 1 jika kosong atau bukan angka
        $indexInt = isset($this->data['index']) && is_numeric($this->data['index']) && $this->data['index'] > 0
            ? (int) $this->data['index']
            : 1;

        $indexPadded = str_pad($indexInt, 3, '0', STR_PAD_LEFT);

        $generatedCode = sprintf(
            '%s/%s/%s/%s',
            strtoupper($lander->code),
            strtoupper($template->category->code),
            strtoupper($template->code),
            $indexPadded
        );

        return new Aset([
            'template_id'   => $template->id,
            'lander_id'   => $lander->id,
            'custom_name'   => $this->data['custom_name'] ?? null,
            'code'          => $generatedCode,
            'condition'     => $this->data['condition'] ?? null,
            'brand'         => $this->data['brand'] ?? null,
            'purchase_year' => $this->data['purchase_year'] ?? null,
            'tarif'         => $this->data['tarif'] ?? null,
            'satuan'        => $this->data['satuan'] ?? null,
            'index'         => $indexInt,
            'status'        => $this->data['status'] ?? 'available',
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import aset selesai. ' . number_format($import->successful_rows) . ' data berhasil diimpor.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' data gagal diimpor.';
        }
        return $body;
    }

    public function storeRecord(): ?Aset
    {
        $record = $this->resolveRecord();

        if (! $record) {
            return null;
        }

        $record->save();

        return $record;
    }

    public static function shouldPersistRecord(): bool
    {
        return true;
    }
}
