<?php

namespace App\Filament\Resources\BmhpPurchaseResource\Pages;

use App\Filament\Resources\BmhpPurchaseResource;
use App\Models\Bmhp;
use App\Models\User;
use App\Notifications\BmhpPurchaseCreated;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CreateBmhpPurchase extends CreateRecord
{
    protected static string $resource = BmhpPurchaseResource::class;

    protected function afterCreate(): void
    {
        // Items are now handled automatically by Filament relationship()
        // Only handle notifications here

        $this->record->loadMissing('supplier');
        $users = User::role(['super-admin', 'owner'])->get();
        foreach ($users as $user) {
            $user->notify(new BmhpPurchaseCreated($this->record));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
