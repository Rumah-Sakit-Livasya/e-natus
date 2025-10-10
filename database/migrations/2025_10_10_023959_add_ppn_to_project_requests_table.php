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
        Schema::table('project_requests', function (Blueprint $table) {
            $table->boolean('with_ppn')->default(false)->after('nilai_invoice');
            $table->decimal('ppn_percentage', 5, 2)->default(11.00)->after('with_ppn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->dropColumn('with_ppn');
            $table->dropColumn('ppn_percentage');
        });
    }
};
