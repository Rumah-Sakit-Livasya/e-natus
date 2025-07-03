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
        Schema::table('rab_closings', function (Blueprint $table) {
            $table->bigInteger('total_anggaran_closing')->default(0)->after('total_realisasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rab_closings', function (Blueprint $table) {
            //
        });
    }
};
