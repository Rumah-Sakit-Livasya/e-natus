<?php

namespace App\Http\Controllers;

use App\Models\ProcurementItem;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;

class ProcurementItemController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Terealisasi,Tidak Terealisasi',
        ]);

        $item = ProcurementItem::findOrFail($id);
        $item->status = $request->input('status');
        $item->save();

        return back()->with('success', 'Status berhasil diupdate');
    }
}
