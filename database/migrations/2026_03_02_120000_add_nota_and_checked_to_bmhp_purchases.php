<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bmhp_purchases', function (Blueprint $table) {
            $table->string('nota_pembelian')->nullable()->after('keterangan');
        });

        Schema::table('bmhp_purchase_items', function (Blueprint $table) {
            $table->boolean('is_checked')->default(true)->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('bmhp_purchase_items', function (Blueprint $table) {
            $table->dropColumn('is_checked');
        });

        Schema::table('bmhp_purchases', function (Blueprint $table) {
            $table->dropColumn('nota_pembelian');
        });
    }
};
