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
        Schema::create('drug_tests', function (Blueprint $table) {
            $table->id();

            // Info Header
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->string('department')->nullable(); // PT/DEPT
            $table->string('no_mcu')->unique();
            $table->date('tanggal_pemeriksaan');

            // Hasil Tes Narkoba
            $table->string('amphetamine')->default('Negatif');
            $table->string('metamphetamine')->default('Negatif');
            $table->string('cocaine')->default('Negatif');
            $table->string('thc')->default('Negatif');
            $table->string('morphine')->default('Negatif');
            $table->string('benzodiazepine')->default('Negatif');

            // Info Footer
            $table->string('analis_kesehatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drug_tests');
    }
};
