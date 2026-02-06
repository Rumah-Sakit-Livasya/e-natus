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
            $table->text('hemoglobin')->nullable();
            $table->text('leukosit')->nullable();
            $table->text('trombosit')->nullable();
            $table->text('hematokrit')->nullable();
            $table->text('eritrosit')->nullable();
            $table->text('mcv')->nullable();
            $table->text('mch')->nullable();
            $table->text('mchc')->nullable();
            $table->text('rdw')->nullable();
            // Hitung Jenis Leukosit
            $table->text('eosinofil')->nullable();
            $table->text('basofil')->nullable();
            $table->text('netrofil_batang')->nullable();
            $table->text('netrofil_segmen')->nullable();
            $table->text('limfosit')->nullable();
            $table->text('monosit')->nullable();
            $table->text('led')->nullable();

            // == URINALISA ==
            $table->text('urine_warna')->nullable();
            $table->text('urine_kejernihan')->nullable();
            $table->text('urine_berat_jenis')->nullable();
            $table->text('urine_ph')->nullable();
            $table->text('urine_protein')->nullable();
            $table->text('urine_glukosa')->nullable();
            $table->text('urine_keton')->nullable();
            $table->text('urine_darah')->nullable();
            $table->text('urine_bilirubin')->nullable();
            $table->text('urine_urobilinogen')->nullable();
            $table->text('urine_nitrit')->nullable();
            $table->text('urine_leukosit_esterase')->nullable();
            // Sedimen
            $table->text('sedimen_leukosit')->nullable();
            $table->text('sedimen_eritrosit')->nullable();
            $table->text('sedimen_silinder')->nullable();
            $table->text('sedimen_sel_epitel')->nullable();
            $table->text('sedimen_kristal')->nullable();
            $table->text('sedimen_bakteria')->nullable();
            $table->text('sedimen_lain_lain')->nullable();

            // == HALAMAN 2 ==

            // == KIMIA KLINIK ==
            $table->text('glukosa_puasa')->nullable();
            $table->text('glukosa_2_jam_pp')->nullable();
            // Fungsi Ginjal
            $table->text('ureum')->nullable();
            $table->text('kreatinin')->nullable();
            $table->text('asam_urat')->nullable();
            $table->text('hbeag')->nullable();
            // Fungsi Hati
            $table->text('sgot')->nullable();
            $table->text('sgpt')->nullable();
            $table->text('alkali_fosfatase')->nullable();
            $table->text('kolinesterase')->nullable();
            $table->text('bilirubin_total')->nullable();
            $table->text('bilirubin_direk')->nullable();
            $table->text('bilirubin_indirek')->nullable();
            // Profil Lemak
            $table->text('kolesterol_total')->nullable();
            $table->text('hdl')->nullable();
            $table->text('ldl')->nullable();
            $table->text('trigliserida')->nullable();
            $table->text('hba1c')->nullable();

            // == SEROLOGI & IMUNOLOGI ==
            $table->text('tpha')->nullable();
            $table->text('vdrl')->nullable();
            $table->text('hbsag')->nullable();
            $table->text('anti_hcv')->nullable();
            $table->text('anti_hbs')->nullable();

            // == SKRINING NARKOBA ==
            $table->text('narkoba_amphetamine')->nullable();
            $table->text('narkoba_thc')->nullable();
            $table->text('narkoba_morphine')->nullable();
            $table->text('narkoba_benzodiazepine')->nullable();
            $table->text('narkoba_methamphetamine')->nullable();
            $table->text('narkoba_cocaine')->nullable();
            $table->text('alkohol_urin')->nullable();

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
