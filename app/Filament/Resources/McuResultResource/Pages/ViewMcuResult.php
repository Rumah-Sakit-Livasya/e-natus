<?php

namespace App\Filament\Resources\McuResultResource\Pages;

use App\Filament\Resources\McuResultResource;
use App\Models\McuResult;
use Filament\Actions;
use Filament\Resources\Pages\Page;

class ViewMcuResult extends Page
{
    protected static string $resource = McuResultResource::class;
    protected static string $view = 'filament.resources.mcu-result-resource.pages.view-mcu-result';

    public McuResult $record;

    // Optional: Add a title to the page
    public function getTitle(): string
    {
        return 'Hasil MCU untuk ' . $this->record->participant->name;
    }
}
