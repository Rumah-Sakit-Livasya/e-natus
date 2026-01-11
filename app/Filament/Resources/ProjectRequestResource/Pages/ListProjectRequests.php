<?php

namespace App\Filament\Resources\ProjectRequestResource\Pages;

use App\Filament\Resources\ProjectRequestResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListProjectRequests extends ListRecords
{
    protected static string $resource = ProjectRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        if (session()->has('success')) {
            Notification::make()
                ->title('Berhasil')
                ->body(session('success'))
                ->success()
                ->send();
        }
    }

    protected function getListeners(): array
    {
        return [
            'markUnavailable',
        ];
    }

    public function markUnavailable($assetId)
    {
        \App\Models\Aset::where('id', $assetId)->update(['is_available' => false]);
        $this->dispatch('notify', type: 'success', message: 'Aset telah ditandai sebagai tidak tersedia.');
    }
}
