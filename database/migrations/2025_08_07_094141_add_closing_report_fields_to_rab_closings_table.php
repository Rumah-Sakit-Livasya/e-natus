<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rab_closings', function (Blueprint $table) {
            if (!Schema::hasColumn('rab_closings', 'jumlah_peserta_awal')) {
                $table->integer('jumlah_peserta_awal')->nullable()->after('status');
            }
            if (!Schema::hasColumn('rab_closings', 'jumlah_peserta_akhir')) {
                $table->integer('jumlah_peserta_akhir')->nullable()->after('jumlah_peserta_awal');
            }
            if (!Schema::hasColumn('rab_closings', 'nilai_invoice_closing')) {
                $table->decimal('nilai_invoice_closing', 15, 2)->nullable()->after('total_anggaran_closing');
            }
            if (!Schema::hasColumn('rab_closings', 'margin_closing')) {
                $table->decimal('margin_closing', 15, 2)->nullable()->after('nilai_invoice_closing');
            }
            if (!Schema::hasColumn('rab_closings', 'dana_operasional_transfer')) {
                $table->decimal('dana_operasional_transfer', 15, 2)->nullable()->after('margin_closing');
            }
            if (!Schema::hasColumn('rab_closings', 'pengeluaran_operasional_closing')) {
                $table->decimal('pengeluaran_operasional_closing', 15, 2)->nullable()->after('dana_operasional_transfer');
            }
            if (!Schema::hasColumn('rab_closings', 'sisa_dana_operasional')) {
                $table->decimal('sisa_dana_operasional', 15, 2)->nullable()->after('pengeluaran_operasional_closing');
            }
            if (!Schema::hasColumn('rab_closings', 'justifikasi')) {
                $table->text('justifikasi')->nullable()->after('keterangan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rab_closings', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah_peserta_awal',
                'jumlah_peserta_akhir',
                'nilai_invoice_closing',
                'margin_closing',
                'dana_operasional_transfer',
                'pengeluaran_operasional_closing',
                'sisa_dana_operasional',
                'justifikasi'
            ]);
        });
    }
};
