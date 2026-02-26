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
            $table->string('purchase_type')->nullable()->change();
            $table->integer('qty')->nullable()->change();
            $table->integer('pcs_per_unit_snapshot')->nullable()->change();
            $table->string('satuan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_bmhp', function (Blueprint $table) {
            $table->string('purchase_type')->nullable(false)->change();
            $table->integer('qty')->nullable(false)->change();
            $table->integer('pcs_per_unit_snapshot')->nullable(false)->change();
            $table->string('satuan')->nullable(false)->change();
        });
    }
};
