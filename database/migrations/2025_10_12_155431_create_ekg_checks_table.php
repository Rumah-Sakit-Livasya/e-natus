<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekg_checks', function (Blueprint $table) {
            $table->id();

            // Info Pasien
            $table->string('no_rm')->unique()->nullable();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->string('instansi')->nullable();
            $table->date('tanggal_pemeriksaan');

            // Hasil Interpretasi
            $table->string('irama')->nullable();
            $table->string('heart_rate')->nullable();
            $table->string('axis')->nullable();
            $table->string('pr_interval')->nullable();
            $table->string('qrs_duration')->nullable();
            $table->string('gel_t')->nullable();
            $table->string('st_t_changes')->nullable();
            $table->string('kelainan')->nullable();
            $table->text('kesimpulan')->nullable();

            // Info Dokter & TTD
            $table->string('dokter_pemeriksa')->nullable();
            $table->string('tanda_tangan')->nullable(); // Path untuk gambar TTD

            // Gambar Hasil EKG (Halaman 2)
            $table->string('gambar_hasil_ekg')->nullable(); // Path untuk gambar hasil EKG

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekg_checks');
    }
};
