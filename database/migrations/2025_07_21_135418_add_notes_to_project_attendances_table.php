<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_attendances', function (Blueprint $table) {
            // TAMBAHKAN BARIS INI
            $table->text('notes')->nullable()->after('foto');
        });
    }

    public function down(): void
    {
        Schema::table('project_attendances', function (Blueprint $table) {
            // TAMBAHKAN BARIS INI
            $table->dropColumn('notes');
        });
    }
};
