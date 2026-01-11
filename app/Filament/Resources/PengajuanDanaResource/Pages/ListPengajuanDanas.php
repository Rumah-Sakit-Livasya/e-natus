<?php

namespace App\Filament\Resources\PengajuanDanaResource\Pages;

use App\Filament\Resources\PengajuanDanaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengajuanDanas extends ListRecords
{
    protected static string $resource = PengajuanDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
