<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usg_abdomen_checks', function (Blueprint $table) {
            $table->id();

            // Info Pasien
            $table->string('no_rm')->unique()->nullable();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->string('instansi')->nullable();
            $table->date('tanggal_pemeriksaan');

            // Hasil Pemeriksaan
            $table->text('hepar')->nullable();
            $table->text('gallbladder')->nullable();
            $table->text('lien')->nullable();
            $table->text('pankreas')->nullable();
            $table->text('ren_kanan')->nullable();
            $table->text('ren_kiri')->nullable();
            $table->text('vesica_urinaria')->nullable();
            $table->text('prostat')->nullable();
            $table->text('catatan_tambahan_1')->nullable();
            $table->text('catatan_tambahan_2')->nullable();
            $table->text('kesimpulan')->nullable();

            // Radiologist & TTD
            $table->string('radiologist')->nullable();
            $table->string('tanda_tangan')->nullable(); // Path TTD

            // Gambar Hasil
            $table->string('gambar_hasil_usg')->nullable(); // Path gambar hasil

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usg_abdomen_checks');
    }
};
