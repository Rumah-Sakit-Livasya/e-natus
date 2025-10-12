<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_checks', function (Blueprint $table) {
            $table->id();

            // == HEADER ==
            $table->string('no_rm')->nullable();
            $table->string('no_lab')->unique()->nullable();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->string('instansi')->nullable();
            $table->date('tanggal_pemeriksaan');

            // == HEMATOLOGI LENGKAP ==
            $table->string('hemoglobin', 50)->nullable();
            $table->string('leukosit', 50)->nullable();
            $table->string('trombosit', 50)->nullable();
            $table->string('hematokrit', 50)->nullable();
            $table->string('eritrosit', 50)->nullable();
            $table->string('mcv', 50)->nullable();
            $table->string('mch', 50)->nullable();
            $table->string('mchc', 50)->nullable();
            $table->string('rdw', 50)->nullable();
            // Hitung Jenis Leukosit
            $table->string('eosinofil', 50)->nullable();
            $table->string('basofil', 50)->nullable();
            $table->string('netrofil_batang', 50)->nullable();
            $table->string('netrofil_segmen', 50)->nullable();
            $table->string('limfosit', 50)->nullable();
            $table->string('monosit', 50)->nullable();
            $table->string('led', 50)->nullable();

            // == URINALISA ==
            $table->string('urine_warna', 50)->nullable();
            $table->string('urine_kejernihan', 50)->nullable();
            $table->string('urine_berat_jenis', 50)->nullable();
            $table->string('urine_ph', 50)->nullable();
            $table->string('urine_protein', 50)->nullable();
            $table->string('urine_glukosa', 50)->nullable();
            $table->string('urine_keton', 50)->nullable();
            $table->string('urine_darah', 50)->nullable();
            $table->string('urine_bilirubin', 50)->nullable();
            $table->string('urine_urobilinogen', 50)->nullable();
            $table->string('urine_nitrit', 50)->nullable();
            $table->string('urine_leukosit_esterase', 50)->nullable();
            // Sedimen
            $table->string('sedimen_leukosit', 50)->nullable();
            $table->string('sedimen_eritrosit', 50)->nullable();
            $table->string('sedimen_silinder', 50)->nullable();
            $table->string('sedimen_sel_epitel', 50)->nullable();
            $table->string('sedimen_kristal', 50)->nullable();
            $table->string('sedimen_bakteria', 50)->nullable();
            $table->string('sedimen_lain_lain', 50)->nullable();

            // == HALAMAN 2 ==

            // == KIMIA KLINIK ==
            $table->string('glukosa_puasa', 50)->nullable();
            $table->string('glukosa_2_jam_pp', 50)->nullable();
            // Fungsi Ginjal
            $table->string('ureum', 50)->nullable();
            $table->string('kreatinin', 50)->nullable();
            $table->string('asam_urat', 50)->nullable();
            $table->string('hbeag', 50)->nullable();
            // Fungsi Hati
            $table->string('sgot', 50)->nullable();
            $table->string('sgpt', 50)->nullable();
            $table->string('alkali_fosfatase', 50)->nullable();
            $table->string('kolinesterase', 50)->nullable();
            $table->string('bilirubin_total', 50)->nullable();
            $table->string('bilirubin_direk', 50)->nullable();
            $table->string('bilirubin_indirek', 50)->nullable();
            // Profil Lemak
            $table->string('kolesterol_total', 50)->nullable();
            $table->string('hdl', 50)->nullable();
            $table->string('ldl', 50)->nullable();
            $table->string('trigliserida', 50)->nullable();
            $table->string('hba1c', 50)->nullable();

            // == SEROLOGI & IMUNOLOGI ==
            $table->string('tpha', 50)->nullable();
            $table->string('vdrl', 50)->nullable();
            $table->string('hbsag', 50)->nullable();
            $table->string('anti_hcv', 50)->nullable();
            $table->string('anti_hbs', 50)->nullable();

            // == SKRINING NARKOBA ==
            $table->string('narkoba_amphetamine', 50)->nullable();
            $table->string('narkoba_thc', 50)->nullable();
            $table->string('narkoba_morphine', 50)->nullable();
            $table->string('narkoba_benzodiazepine', 50)->nullable();
            $table->string('narkoba_methamphetamine', 50)->nullable();
            $table->string('narkoba_cocaine', 50)->nullable();
            $table->string('alkohol_urin', 50)->nullable();

            // == FOOTER ==
            $table->string('penanggung_jawab')->nullable();
            $table->string('tanda_tangan')->nullable(); // Path untuk gambar TTD

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_checks');
    }
};
