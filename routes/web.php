<?php

use App\Filament\Pages\ProjectFinanceComparison;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProcurementItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectRequestActionController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProjectRealisationController;
use App\Models\Aset;
use Illuminate\Support\Facades\DB;
use App\Filament\Resources\ProjectRequestResource\Pages\CompareRab;


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


// Gunakan grup middleware yang SAMA dengan panel Filament Anda
// Ini memastikan halaman Anda memiliki layout, otentikasi, dan tema yang sama.
// Ganti 'dashboard' dengan ID panel Anda jika berbeda (misalnya: 'dashboard')
Route::domain(config('filament.panels.dashboard.domain'))
    ->middleware(config('filament.panels.dashboard.middleware'))
    ->prefix(config('filament.panels.dashboard.path')) // Otomatis mengambil path seperti '/dashboard' atau '/dashboard'
    ->group(function () {
        // Daftarkan rute kustom Anda di sini
        // URL-nya akan menjadi: /dashboard/project-comparison/{record}
        Route::get('/project-comparison/{record}', ProjectFinanceComparison::class)
            ->name('filament.admin.pages.project-finance-comparison'); // Beri nama yang unik
    });
