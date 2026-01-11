<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treadmill_checks', function (Blueprint $table) {
            $table->id();

            // Info Pasien
            $table->string('no_rm')->unique()->nullable();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->string('instansi')->nullable();
            $table->date('tanggal_pemeriksaan');

            // Hasil Pemeriksaan
            $table->string('metode')->nullable();
            $table->string('ekg_resting')->nullable();
            $table->string('ekg_exercise_st_change')->nullable();
            $table->string('ekg_exercise_aritmia')->nullable();
            $table->string('td_awal')->nullable();
            $table->string('td_tertinggi')->nullable();
            $table->string('indikasi_berhenti')->nullable();
            $table->string('target_hr')->nullable();
            $table->string('tercapai_hr')->nullable();
            $table->integer('lama_tes_menit')->nullable();
            $table->integer('lama_tes_detik')->nullable();
            $table->string('kapasitas_aerobik')->nullable();
            $table->string('kelas_fungsional')->nullable();
            $table->string('tingkat_kebugaran')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->text('saran')->nullable();

            // Cardiologist & TTD
            $table->string('cardiologist')->nullable();
            $table->string('tanda_tangan')->nullable(); // Path TTD

            // Gambar Hasil
            $table->string('gambar_hasil_treadmill')->nullable(); // Path gambar hasil

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treadmill_checks');
    }
};
