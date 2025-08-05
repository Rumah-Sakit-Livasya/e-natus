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
        // 'mcu_attachments' adalah nama tabel Anda
        Schema::table('mcu_attachments', function (Blueprint $table) {
            // Mengubah kolom yang sudah ada agar bisa menerima nilai NULL
            $table->string('original_filename')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mcu_attachments', function (Blueprint $table) {
            // Kembalikan seperti semula jika migrasi di-rollback
            $table->string('original_filename')->nullable(false)->change();
        });
    }
};
