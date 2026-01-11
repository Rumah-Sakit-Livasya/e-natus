<?php

namespace App\Filament\Imports;

use App\Models\Template;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TemplateAsetImporter extends Importer
{
    protected static ?string $model = Template::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name'),
            ImportColumn::make('code'),
            ImportColumn::make('category_id')->rules(['exists:categories,id']),
        ];
    }


    public function resolveRecord(): ?Template
    {
        // Membuat atau memperbarui Template berdasarkan code (unik)
        return Template::updateOrCreate(
            ['code' => $this->data['code']],
            [
                'name' => $this->data['name'],
                'category_id' => $this->data['category_id'],
            ]
        );
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import template aset selesai. ' . number_format($import->successful_rows) . ' data berhasil diimpor.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' data gagal diimpor.';
        }

        return $body;
    }
}
