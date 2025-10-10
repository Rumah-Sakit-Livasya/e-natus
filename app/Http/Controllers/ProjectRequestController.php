<?php

namespace App\Http\Controllers;

use App\Models\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

class ProjectRequestController extends Controller
{
    /**
     * Menampilkan halaman cetak invoice untuk Project Request.
     */
    public function printInvoice(ProjectRequest $projectRequest): View
    {
        // Load relasi client untuk menghindari query N+1
        $projectRequest->load('client');

        // =================== PERUBAHAN DIMULAI DI SINI ===================
        $total = $projectRequest->nilai_invoice;
        $ppn = 0;
        $ppnPercentage = 0;

        // Lakukan kalkulasi PPN hanya jika diaktifkan
        if ($projectRequest->with_ppn) {
            $ppnPercentage = $projectRequest->ppn_percentage ?? 11; // Default 11% jika null
            $ppn = $total * ($ppnPercentage / 100);
        }

        $grandTotal = $total + $ppn;
        // =================== PERUBAHAN SELESAI DI SINI ===================

        // Hitung durasi pembayaran
        $paymentDuration = $projectRequest->created_at->diffInDays($projectRequest->due_date);

        return ViewFacade::make('invoices.project-request', [
            'project' => $projectRequest,
            'total' => $total,
            'ppn' => $ppn,
            'ppnPercentage' => $ppnPercentage, // Kirim persentase ke view
            'grandTotal' => $grandTotal,
            'paymentDuration' => $paymentDuration,
        ]);
    }
}
