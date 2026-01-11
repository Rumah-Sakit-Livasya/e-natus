<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rab_closing_fee_petugas_items', function (Blueprint $table) {
            // Tambahkan kolom untuk menyimpan path file setelah kolom 'price'
            $table->string('attachment')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('rab_closing_fee_petugas_items', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};
