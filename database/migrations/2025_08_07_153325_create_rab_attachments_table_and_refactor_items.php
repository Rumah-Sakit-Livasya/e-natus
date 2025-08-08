<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel polimorfik untuk semua attachment
        Schema::create('rab_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');

            // Ini adalah bagian polimorfik:
            // 'attachable_id' akan menyimpan ID dari item (misal: 5)
            // 'attachable_type' akan menyimpan nama modelnya (misal: App\Models\RabClosingOperasionalItem)
            $table->morphs('attachable');

            $table->timestamps();
        });

        // 2. Hapus kolom 'attachment' yang lama dari tabel item operasional
        Schema::table('rab_closing_operasional_items', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });

        // 3. Hapus kolom 'attachment' yang lama dari tabel item fee
        Schema::table('rab_closing_fee_petugas_items', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }

    public function down(): void
    {
        // Lakukan kebalikannya jika rollback
        Schema::dropIfExists('rab_attachments');

        Schema::table('rab_closing_operasional_items', function (Blueprint $table) {
            $table->string('attachment')->nullable();
        });

        Schema::table('rab_closing_fee_petugas_items', function (Blueprint $table) {
            $table->string('attachment')->nullable();
        });
    }
};
