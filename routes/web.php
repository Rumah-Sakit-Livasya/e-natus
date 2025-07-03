<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProcurementItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectRequestActionController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProjectRealisationController;
use App\Models\Aset;
use Illuminate\Support\Facades\DB;

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
    Route::get('/print-realisasi-rab/{project}', [PrintController::class, 'printRealisasiRab'])->name('print-realisasi-rab');
    Route::get('/project-requests/{project}/realisasi-rab/create', [ProjectRealisationController::class, 'create'])
        ->name('project.realisasi-rab.create');

    Route::post('/realisation-rab-items', [ProjectRealisationController::class, 'store'])
        ->name('realisation-rab-items.store');
});


Route::get('/test-db-insert', function () {
    try {
        $valueToInsert = 1000000; // Nilai numerik murni

        DB::table('pengajuan_danas')->insert([
            'project_request_id' => 1, // Ganti dengan ID proyek yang ada
            'user_id' => 1, // Ganti dengan ID user yang ada
            'tujuan' => 'Test Insert Manual',
            'jumlah_diajukan' => $valueToInsert,
            'tanggal_pengajuan' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ambil data yang baru saja dimasukkan
        $result = DB::table('pengajuan_danas')->latest()->first();

        dd($result);
    } catch (\Exception $e) {
        dd($e->getMessage());
    }
});
