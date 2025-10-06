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
        // Perintah ini akan mengubah tabel 'rab_closings' yang sudah ada
        Schema::table('rab_closings', function (Blueprint $table) {
            // Menambahkan kolom untuk catatan/keterangan setelah kolom 'justifikasi'
            // Kolom ini mungkin sudah ada dari migrasi lama Anda,
            // jika ya, Anda bisa menghapus baris ini.
            if (!Schema::hasColumn('rab_closings', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('justifikasi');
            }

            // Menambahkan kolom untuk menyimpan path file dokumentasi dalam format JSON
            if (!Schema::hasColumn('rab_closings', 'documentation')) {
                $table->json('documentation')->nullable()->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rab_closings', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            if (Schema::hasColumn('rab_closings', 'documentation')) {
                $table->dropColumn('documentation');
            }
            if (Schema::hasColumn('rab_closings', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};
