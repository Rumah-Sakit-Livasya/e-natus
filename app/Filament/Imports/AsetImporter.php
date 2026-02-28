<?php

namespace App\Filament\Imports;

use App\Models\Aset;
use App\Models\Lander;
use App\Models\Template;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\ValidationException;

class AsetImporter extends Importer
{
    protected static ?string $model = Aset::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id')
                ->label('ID')
                ->integer()
                ->rules(['nullable', 'integer', 'exists:aset,id']),
            ImportColumn::make('template_name')
                ->label('Nama Template')
                ->rules(['nullable', 'string', 'max:255', 'exists:templates,name'])
                ->requiredMappingForNewRecordsOnly(),
            ImportColumn::make('lander_name')
                ->label('Nama Lander')
                ->rules(['nullable', 'string', 'max:255', 'exists:landers,name'])
                ->requiredMappingForNewRecordsOnly(),
            ImportColumn::make('custom_name')
                ->label('Nama Aset')
                ->rules(['nullable', 'string', 'max:255'])
                ->requiredMappingForNewRecordsOnly(),
            ImportColumn::make('type')
                ->label('Tipe')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('serial_number')
                ->label('Serial Number')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('code')
                ->label('Kode')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('condition')
                ->label('Kondisi')
                ->rules(['nullable', 'string', 'max:255'])
                ->requiredMappingForNewRecordsOnly(),
            ImportColumn::make('brand')
                ->label('Merk')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('purchase_year')
                ->label('Tahun Pembelian')
                ->integer()
                ->rules(['nullable', 'integer', 'min:1900', 'max:2100'])
                ->requiredMappingForNewRecordsOnly(),
            ImportColumn::make('tarif')
                ->label('Tarif')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('harga_sewa')
                ->label('Harga Sewa')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('satuan')
                ->label('Satuan')
                ->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('index')
                ->label('Index')
                ->integer()
                ->rules(['nullable', 'integer', 'min:1']),
            ImportColumn::make('status')
                ->label('Status')
                ->rules(['nullable', 'in:available,unavailable']),
        ];
    }

    public function resolveRecord(): ?Aset
    {
        $id = $this->toIntOrNull($this->data['id'] ?? null);
        $code = $this->normalizeString($this->data['code'] ?? null);

        $record = null;
        if ($id) {
            $record = Aset::withTrashed()->find($id);
        }

        if (! $record && filled($code)) {
            $record = Aset::withTrashed()->where('code', $code)->first();
        }

        if ($record) {
            if (method_exists($record, 'trashed') && $record->trashed()) {
                $record->restore();
            }

            return $record;
        }

        return new Aset();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import aset selesai. ' . number_format($import->successful_rows) . ' data berhasil diimpor.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' data gagal diimpor.';
        }
        return $body;
    }

    public function fillRecord(): void
    {
        $templateName = $this->normalizeString($this->data['template_name'] ?? null);
        $landerName = $this->normalizeString($this->data['lander_name'] ?? null);

        $template = filled($templateName)
            ? Template::query()->where('name', $templateName)->first()
            : null;
        $lander = filled($landerName)
            ? Lander::query()->where('name', $landerName)->first()
            : null;

        if (! $this->record->exists) {
            $missing = [];
            if (! $template) {
                $missing['template_name'] = 'Nama Template wajib diisi untuk data baru dan harus valid.';
            }
            if (! $lander) {
                $missing['lander_name'] = 'Nama Lander wajib diisi untuk data baru dan harus valid.';
            }
            if (! filled($this->normalizeString($this->data['custom_name'] ?? null))) {
                $missing['custom_name'] = 'Nama Aset wajib diisi untuk data baru.';
            }
            if (! filled($this->normalizeString($this->data['condition'] ?? null))) {
                $missing['condition'] = 'Kondisi wajib diisi untuk data baru.';
            }
            if (! filled($this->data['purchase_year'] ?? null)) {
                $missing['purchase_year'] = 'Tahun Pembelian wajib diisi untuk data baru.';
            }

            if (! empty($missing)) {
                throw ValidationException::withMessages($missing);
            }
        }

        if (filled($templateName) && ! $template) {
            throw ValidationException::withMessages([
                'template_name' => "Template '{$templateName}' tidak ditemukan.",
            ]);
        }

        if (filled($landerName) && ! $lander) {
            throw ValidationException::withMessages([
                'lander_name' => "Lander '{$landerName}' tidak ditemukan.",
            ]);
        }

        if ($template) {
            $this->record->template_id = $template->id;
        }
        if ($lander) {
            $this->record->lander_id = $lander->id;
        }

        $this->fillWhenPresent('custom_name');
        $this->fillWhenPresent('type');
        $this->fillWhenPresent('serial_number');
        $this->fillWhenPresent('condition');
        $this->fillWhenPresent('brand');
        $this->fillWhenPresent('purchase_year', fn($value) => (int) $value);
        $this->fillWhenPresent('tarif', fn($value) => $this->toNumericOrNull($value));
        $this->fillWhenPresent('harga_sewa', fn($value) => $this->toNumericOrNull($value));
        $this->fillWhenPresent('satuan');
        $this->fillWhenPresent('index', fn($value) => (int) $value);

        $code = $this->normalizeString($this->data['code'] ?? null);
        if (filled($code)) {
            $this->record->code = strtoupper($code);
        }

        $status = $this->normalizeString($this->data['status'] ?? null);
        if (filled($status)) {
            $status = strtolower($status);
            if (in_array($status, ['available', 'unavailable'], true)) {
                $this->record->status = $status;
            }
        } elseif (! $this->record->exists) {
            $this->record->status = 'available';
        }
    }

    protected function fillWhenPresent(string $attribute, ?callable $transform = null): void
    {
        if (! array_key_exists($attribute, $this->data)) {
            return;
        }

        $value = $this->data[$attribute];
        if ($value === null || $value === '') {
            return;
        }

        $this->record->{$attribute} = $transform ? $transform($value) : $value;
    }

    protected function normalizeString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    protected function toIntOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    protected function toNumericOrNull(mixed $value): int|float|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return $value + 0;
        }

        $normalized = preg_replace('/[^0-9.\-]/', '', (string) $value);

        if ($normalized === '' || ! is_numeric($normalized)) {
            return null;
        }

        return $normalized + 0;
    }
}
