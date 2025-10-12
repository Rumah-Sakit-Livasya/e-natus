<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rontgen_checks', function (Blueprint $table) {
            $table->id();

            // Info Header
            $table->string('no_rontgen')->unique()->nullable();
            $table->string('no_rm')->nullable();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->string('instansi')->nullable();
            $table->date('tanggal_pemeriksaan');

            // Hasil Pemeriksaan
            $table->text('temuan')->nullable(); // Untuk list temuan (Yth, TS)
            $table->text('kesan')->nullable();

            // Radiologist & TTD
            $table->string('radiologist')->nullable();
            $table->string('tanda_tangan')->nullable(); // Path TTD

            // Gambar Hasil
            $table->string('gambar_hasil_rontgen')->nullable(); // Path gambar hasil

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rontgen_checks');
    }
};
