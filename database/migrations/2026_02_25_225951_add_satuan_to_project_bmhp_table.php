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
        Schema::table('project_bmhp', function (Blueprint $table) {
            $table->string('satuan')->nullable()->after('pcs_per_unit_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_bmhp', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });
    }
};
