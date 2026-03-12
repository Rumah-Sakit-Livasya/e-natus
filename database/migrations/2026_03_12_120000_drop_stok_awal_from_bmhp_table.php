<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bmhp', function (Blueprint $table) {
            if (Schema::hasColumn('bmhp', 'stok_awal')) {
                $table->dropColumn('stok_awal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bmhp', function (Blueprint $table) {
            if (! Schema::hasColumn('bmhp', 'stok_awal')) {
                $table->integer('stok_awal')->default(0)->after('pcs_per_unit');
            }
        });
    }
};
