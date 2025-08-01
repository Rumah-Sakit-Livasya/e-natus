<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal_absensi');
            $table->text('alasan');
            $table->string('foto_bukti')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('submitted_by')->constrained('users')->comment('User yang menginput pengajuan');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->comment('User yang menyetujui/menolak');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable()->comment('Catatan saat review');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_submissions');
    }
};
