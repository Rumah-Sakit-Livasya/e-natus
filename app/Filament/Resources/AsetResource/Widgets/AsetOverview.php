<?php

namespace App\Filament\Resources\AsetResource\Widgets;

use App\Models\Aset;
use Filament\Actions\Action;
use Filament\Widgets\Widget;

class AsetOverview extends Widget
{
    protected static string $view = 'filament.widgets.aset-overview';
    protected int | string | array $columnSpan = 'full';

    public function showDetailModal(): void
    {
        $this->dispatch('open-aset-detail-modal');
    }

    protected function getActions(): array
    {
        return [
            Action::make('detail')
                ->label('Lihat Detail Aset')
                ->icon('heroicon-o-eye')
                ->action(fn() => $this->showDetailModal()),
        ];
    }

    public function getAvailableCount(): int
    {
        return Aset::where('status', 'available')->count();
    }

    public function getUnavailableList()
    {
        return Aset::where('status', 'unavailable')->count();
    }
}
