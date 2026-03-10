<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usg_abdomen_checks', function (Blueprint $table) {
            $table->json('gambar_hasil_usg_lampiran')->nullable()->after('gambar_hasil_usg_4');
        });

        Schema::table('usg_mammae_checks', function (Blueprint $table) {
            $table->json('gambar_hasil_usg_lampiran')->nullable()->after('gambar_hasil_usg_6');
        });
    }

    public function down(): void
    {
        Schema::table('usg_mammae_checks', function (Blueprint $table) {
            $table->dropColumn('gambar_hasil_usg_lampiran');
        });

        Schema::table('usg_abdomen_checks', function (Blueprint $table) {
            $table->dropColumn('gambar_hasil_usg_lampiran');
        });
    }
};
