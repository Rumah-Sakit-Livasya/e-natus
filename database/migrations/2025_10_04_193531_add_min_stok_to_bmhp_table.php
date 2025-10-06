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
        Schema::table('bmhp', function (Blueprint $table) {
            // Menambahkan kolom untuk stok minimum setelah kolom stok_sisa
            $table->integer('min_stok')->default(0)->after('stok_sisa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bmhp', function (Blueprint $table) {
            $table->dropColumn('min_stok');
        });
    }
};
