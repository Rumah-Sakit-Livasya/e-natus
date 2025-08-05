<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcu_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignId('project_request_id')->constrained('project_requests')->cascadeOnDelete();
            $table->string('no_mcu');
            $table->date('tanggal_mcu');
            $table->json('anamnesa')->nullable();
            $table->json('riwayat_penyakit_dan_gaya_hidup')->nullable();
            $table->json('hasil_pemeriksaan_vital_sign')->nullable();
            $table->json('hasil_pemeriksaan_fisik_dokter')->nullable();
            $table->json('hasil_laboratorium')->nullable();
            $table->json('hasil_pemeriksaan_penunjang')->nullable();
            $table->json('status_kesehatan')->nullable();
            $table->json('kesimpulan_dan_saran')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcu_results');
    }
};
