<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus foreign key constraint dulu jika perlu (opsional, tapi lebih aman)
        // Schema::table('realisation_rab_items', function (Blueprint $table) {
        //     $table->dropForeign(['rencana_anggaran_biaya_id']);
        // });

        // Langsung hapus tabelnya
        Schema::dropIfExists('realisation_rab_items');
    }

    public function down(): void
    {
        // Jika Anda ingin bisa rollback, Anda harus membuat ulang tabel
        // realisation_rab_items di sini. Tapi karena kita menghapusnya
        // secara permanen, method down bisa dikosongkan.
    }
};
