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
use App\Http\Controllers\AudiometryCheckController;
use App\Http\Controllers\DrugTestController;
use App\Http\Controllers\EkgCheckController;
use App\Http\Controllers\LabCheckController;
use App\Http\Controllers\ParticipantReportController;
use App\Http\Controllers\ProjectRequestController;
use App\Http\Controllers\RabClosingController;
use App\Http\Controllers\RontgenCheckController;
use App\Http\Controllers\SpirometryCheckController;
use App\Http\Controllers\TreadmillCheckController;
use App\Http\Controllers\UsgAbdomenCheckController;
use App\Http\Controllers\UsgMammaeCheckController;

// Redirect root to dashboard login
Route::get('/', function () {
    return redirect('/dashboard');
});

Route::post('/project-requests/{id}/approve', [ProjectRequestActionController::class, 'approve'])->name('project-requests.approve');
Route::post('/project-requests/{id}/reject', [ProjectRequestActionController::class, 'reject'])->name('project-requests.reject');
Route::get('/rab-closings/{record}/print', [RabClosingController::class, 'print'])
    ->middleware('auth') // Pastikan hanya pengguna yang login yang bisa akses
    ->name('rab-closing.print');
Route::get('/project-requests/{projectRequest}/invoice', [ProjectRequestController::class, 'printInvoice'])
    ->name('project-requests.invoice');
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
    Route::get('/print-asset-barcodes', [PrintController::class, 'printAssetBarcodes'])->name('print-asset-barcodes');
    Route::get('/print-realisasi-rab/{project}', [PrintController::class, 'printRealisasiRab'])->name('print-realisasi-rab');
    Route::get('/project-requests/{project}/realisasi-rab/create', [ProjectRealisationController::class, 'create'])
        ->name('project.realisasi-rab.create');

    Route::post('/realisation-rab-items', [ProjectRealisationController::class, 'store'])
        ->name('realisation-rab-items.store');

    Route::get('/audiometry-checks/{record}/print', [AudiometryCheckController::class, 'print'])
        ->name('audiometry.print');

    Route::get('/drug-tests/{record}/print', [DrugTestController::class, 'print'])->name('drug-test.print');

    Route::get('/ekg-checks/{record}/print', [EkgCheckController::class, 'print'])->name('ekg.print');

    Route::get('/lab-checks/{record}/print', [LabCheckController::class, 'print'])->name('lab.print');

    Route::get('/spirometry-checks/{record}/print', [SpirometryCheckController::class, 'print'])->name('spirometri.print');

    Route::get('/rontgen-checks/{record}/print', [RontgenCheckController::class, 'print'])->name('rontgen.print');

    Route::get('/treadmill-checks/{record}/print', [TreadmillCheckController::class, 'print'])->name('treadmill.print');

    Route::get('/usg-abdomen-checks/{record}/print', [UsgAbdomenCheckController::class, 'print'])->name('usg.print');

    Route::get('/usg-mammae-checks/{record}/print', [UsgMammaeCheckController::class, 'print'])->name('usg-mammae.print');

    Route::get('/participants/{participant}/print-summary', [ParticipantReportController::class, 'printSummary'])
        ->name('participant.report.print');
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

// Debug route - hapus setelah selesai debug
Route::middleware(['web', 'auth'])->get('/debug/current-user-notifications', function () {
    $user = auth()->user();
    $notifications = $user->notifications()->latest()->get();

    return response()->json([
        'user_id' => $user->id,
        'user_name' => $user->name,
        'total_notifications' => $user->notifications()->count(),
        'unread_notifications' => $user->unreadNotifications()->count(),
        'latest_notifications' => $notifications->map(fn($n) => [
            'id' => $n->id,
            'title' => $n->data['title'] ?? 'No title',
            'message' => $n->data['message'] ?? 'No message',
            'read_at' => $n->read_at,
            'created_at' => $n->created_at->diffForHumans(),
        ])->toArray()
    ]);
});
