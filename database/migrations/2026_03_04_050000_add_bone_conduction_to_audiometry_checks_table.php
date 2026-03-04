<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audiometry_checks', function (Blueprint $table) {
            $table->integer('ad_bc_250')->nullable()->after('ad_ac_8000');
            $table->integer('ad_bc_500')->nullable();
            $table->integer('ad_bc_1000')->nullable();
            $table->integer('ad_bc_2000')->nullable();
            $table->integer('ad_bc_3000')->nullable();
            $table->integer('ad_bc_4000')->nullable();
            $table->integer('ad_bc_6000')->nullable();
            $table->integer('ad_bc_8000')->nullable();

            $table->integer('as_bc_250')->nullable()->after('as_ac_8000');
            $table->integer('as_bc_500')->nullable();
            $table->integer('as_bc_1000')->nullable();
            $table->integer('as_bc_2000')->nullable();
            $table->integer('as_bc_3000')->nullable();
            $table->integer('as_bc_4000')->nullable();
            $table->integer('as_bc_6000')->nullable();
            $table->integer('as_bc_8000')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('audiometry_checks', function (Blueprint $table) {
            $table->dropColumn([
                'ad_bc_250',
                'ad_bc_500',
                'ad_bc_1000',
                'ad_bc_2000',
                'ad_bc_3000',
                'ad_bc_4000',
                'ad_bc_6000',
                'ad_bc_8000',
                'as_bc_250',
                'as_bc_500',
                'as_bc_1000',
                'as_bc_2000',
                'as_bc_3000',
                'as_bc_4000',
                'as_bc_6000',
                'as_bc_8000',
            ]);
        });
    }
};
