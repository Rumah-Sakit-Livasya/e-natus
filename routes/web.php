<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectRequestActionController;
use App\Models\Aset;


// Redirect root to dashboard login
Route::get('/', function () {
    return redirect('/dashboard');
});

Route::post('/project-requests/{id}/approve', [ProjectRequestActionController::class, 'approve'])->name('project-requests.approve');
Route::post('/project-requests/{id}/reject', [ProjectRequestActionController::class, 'reject'])->name('project-requests.reject');

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/aset/{aset}/mark-unavailable', function (Aset $aset) {
        $aset->update(['status' => 'unavailable']);
        return redirect()->back()->with('status', 'Aset ditandai sebagai unavailable');
    })->name('aset.mark-unavailable');

    // routes/web.php
    Route::patch('/procurement-items/{id}/update-status', [ProcurementItemController::class, 'updateStatus'])->name('procurement-items.updateStatus');
});
