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
        Schema::table('rab_operasional_items', function (Blueprint $table) {
            $table->boolean('is_vendor_rental')->default(false)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rab_operasional_items', function (Blueprint $table) {
            $table->dropColumn('is_vendor_rental');
        });
    }
};
