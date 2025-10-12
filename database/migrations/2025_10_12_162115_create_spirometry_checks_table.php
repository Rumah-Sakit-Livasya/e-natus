<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spirometry_checks', function (Blueprint $table) {
            $table->id();

            // Info Pasien
            $table->string('no_rm')->unique()->nullable();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->string('instansi')->nullable();
            $table->date('tanggal_pemeriksaan');

            // Hasil Pemeriksaan (Nilai & Prediksi)
            $table->string('vc_nilai')->nullable();
            $table->string('vc_prediksi')->nullable();
            $table->string('fvc_nilai')->nullable();
            $table->string('fvc_prediksi')->nullable();
            $table->string('fev1_nilai')->nullable();
            $table->string('fev1_prediksi')->nullable();
            // FEV1/FVC dan % lainnya akan dihitung otomatis, tidak perlu disimpan

            // Kesan & Saran
            $table->text('kesan')->nullable();
            $table->text('saran')->nullable();

            // Dokter & TTD
            $table->string('dokter_pemeriksa')->nullable();
            $table->string('tanda_tangan')->nullable(); // Path TTD

            // Gambar Hasil
            $table->string('gambar_hasil_spirometri')->nullable(); // Path gambar hasil

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spirometry_checks');
    }
};
