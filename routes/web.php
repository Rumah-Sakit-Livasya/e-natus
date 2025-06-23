<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProcurementItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectRequestActionController;
use App\Http\Controllers\PrintController;
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


    Route::get('/print-rab/{project}', [PrintController::class, 'printRAB'])->name('print-rab');
    Route::get('/print-assets', [PrintController::class, 'printAssets'])->name('print-assets');
});
