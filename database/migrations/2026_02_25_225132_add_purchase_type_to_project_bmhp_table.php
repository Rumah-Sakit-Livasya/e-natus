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
            $table->string('purchase_type')->default('pcs')->after('bmhp_id');
            $table->integer('qty')->default(0)->after('purchase_type');
            $table->integer('pcs_per_unit_snapshot')->default(0)->after('qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_bmhp', function (Blueprint $table) {
            $table->dropColumn(['purchase_type', 'qty', 'pcs_per_unit_snapshot']);
        });
    }
};
