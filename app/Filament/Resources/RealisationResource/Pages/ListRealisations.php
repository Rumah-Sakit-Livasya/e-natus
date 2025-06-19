<?php

namespace App\Filament\Resources\RealisationResource\Pages;

use App\Filament\Resources\RealisationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRealisations extends ListRecords
{
    protected static string $resource = RealisationResource::class;

    public function mount(): void
    {
        $procurementId = request()->query('procurement_id');

        if ($procurementId) {
            RealisationResource::$filterProcurementId = (int) $procurementId;
        }
    }
}
