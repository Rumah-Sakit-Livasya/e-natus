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
        Schema::table('participants', function (Blueprint $table) {
            // Tambahkan kolom 'photo' setelah kolom 'name'
            // Nullable berarti kolom ini tidak wajib diisi
            $table->string('photo')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Definisikan cara untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('photo');
        });
    }
};
