<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audiometry_checks', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm')->unique();

            // Relasi ke tabel participant
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();

            $table->string('instansi')->nullable();
            $table->date('tanggal_pemeriksaan');

            // Hasil Telinga Kanan (Air Conduction)
            $table->integer('ad_ac_250')->nullable();
            $table->integer('ad_ac_500')->nullable();
            $table->integer('ad_ac_1000')->nullable();
            $table->integer('ad_ac_2000')->nullable();
            $table->integer('ad_ac_3000')->nullable();
            $table->integer('ad_ac_4000')->nullable();
            $table->integer('ad_ac_6000')->nullable();
            $table->integer('ad_ac_8000')->nullable();

            // Hasil Telinga Kiri (Air Conduction)
            $table->integer('as_ac_250')->nullable();
            $table->integer('as_ac_500')->nullable();
            $table->integer('as_ac_1000')->nullable();
            $table->integer('as_ac_2000')->nullable();
            $table->integer('as_ac_3000')->nullable();
            $table->integer('as_ac_4000')->nullable();
            $table->integer('as_ac_6000')->nullable();
            $table->integer('as_ac_8000')->nullable();

            // Derajat Ambang Dengar
            $table->string('derajat_ad')->nullable(); // Telinga Kanan
            $table->string('derajat_as')->nullable(); // Telinga Kiri

            // Kesimpulan & Saran
            $table->text('kesimpulan')->nullable();
            $table->text('saran')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audiometry_checks');
    }
};
