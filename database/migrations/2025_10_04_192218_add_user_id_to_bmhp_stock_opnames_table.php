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
        Schema::table('bmhp_stock_opnames', function (Blueprint $table) {
            // Menambahkan kolom user_id setelah kolom 'keterangan'
            $table->foreignId('user_id')
                ->nullable() // Dibuat nullable agar data lama tidak error
                ->after('keterangan')
                ->constrained('users') // Membuat foreign key ke tabel 'users'
                ->nullOnDelete(); // Jika user dihapus, user_id di sini akan jadi NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bmhp_stock_opnames', function (Blueprint $table) {
            // Hapus foreign key constraint sebelum menghapus kolom
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
