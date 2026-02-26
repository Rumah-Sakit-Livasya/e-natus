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
        Schema::table('rab_closing_bmhp_items', function (Blueprint $table) {
            $table->string('sisa_purchase_type')->default('pcs')->after('satuan');
            $table->integer('sisa_qty')->default(0)->after('sisa_purchase_type');
            $table->integer('pcs_per_unit_snapshot')->default(1)->after('sisa_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rab_closing_bmhp_items', function (Blueprint $table) {
            $table->dropColumn(['sisa_purchase_type', 'sisa_qty', 'pcs_per_unit_snapshot']);
        });
    }
};
