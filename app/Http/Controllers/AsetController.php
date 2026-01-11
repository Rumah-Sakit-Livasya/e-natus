<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Filament\Notifications\Notification;

class AsetController extends Controller
{

    public function markUnavailable($id)
    {
        $aset = \App\Models\Aset::findOrFail($id);
        $aset->condition = 'unavailable';
        $aset->save();

        Notification::make()
            ->title('Berhasil')
            ->body('Status aset "' . $aset->custom_name . '" berhasil diubah menjadi unavailable.')
            ->success()
            ->send();

        return back();
    }
}
